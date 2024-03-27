<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use ReflectionClass;
use Vivarium\Assertion\Boolean\IsTrue;
use Vivarium\Assertion\String\IsClass;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Comparator\Priority;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Container;
use Vivarium\Container\Definition;
use Vivarium\Container\Exception\PropertyNotFound;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\Interception;
use Vivarium\Container\Interception\ImmutableMethodInterception;
use Vivarium\Container\Interception\MethodInterception;
use Vivarium\Container\Interception\MutableMethodInterception;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\Constructor;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\FactoryMethodCall;
use Vivarium\Container\Reflection\Method;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Container\Reflection\StaticMethodCall;

use function array_map;

final class Prototype implements Definition
{
    private CreationalMethod $constructor;

    /** @var Map<string, Provider> */
    private Map $properties;

    /** @var Queue<ValueAndPriority<MethodInterception>> */
    private Queue $methods;

    /** @param class-string $class */
    public function __construct(private string $class)
    {
        (new IsClass())
            ->assert($class);

        (new IsTrue())
            ->assert(
                (new ReflectionClass($class))
                    ->isInstantiable(),
                'Expectec Prototype class to be instantiable.',
            );

        $this->constructor = new Constructor($class);
        $this->properties  = new HashMap();
        $this->methods     = new PriorityQueue(new SortableComparator());
    }

    public function provide(Container $container): mixed
    {
        $instance = $this->constructor->invoke($container);

        $reflector = new ReflectionClass($instance);
        foreach ($this->properties as $property => $provider) {
            if (! $reflector->hasProperty($property)) {
                throw new PropertyNotFound($this->class, $property);
            }

            $reflector->getProperty($property)
                      ->setValue($instance, $provider->provide($container));
        }

        foreach ($this->methods as $method) {
            $instance = $method->getValue()
                               ->intercept($container, $instance);
        }

        return $instance;
    }

    public function bindConstructorFactory(
        string $class,
        string $method,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): self {
        $prototype              = clone $this;
        $prototype->constructor = new FactoryMethodCall($class, $method, $tag, $context);

        return $prototype;
    }

    public function bindConstructorStaticFactory(string $class, string $method): self
    {
        $prototype              = clone $this;
        $prototype->constructor = new StaticMethodCall($class, $method);

        return $prototype;
    }

    /** @return Binder<Prototype> */
    public function bindParameter(string $parameter): Binder
    {
        return new GenericBinder(function (Provider $provider) use ($parameter): Prototype {
            $prototype              = clone $this;
            $prototype->constructor = $this->constructor
                ->bindParameter($parameter)
                ->toProvider($provider);

            return $prototype;
        });
    }

    /** @return Binder<Prototype> */
    public function bindProperty(string $property): Binder
    {
        return new GenericBinder(function (Provider $provider) use ($property): Prototype {
            $prototype             = clone $this;
            $prototype->properties = $prototype->properties->put($property, $provider);

            return $prototype;
        });
    }

    /** @param callable(Method):Method|null $define */
    public function bindMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL): self
    {
        $prototype          = clone $this;
        $prototype->methods = $prototype->methods->enqueue(
            new ValueAndPriority(
                new MutableMethodInterception($this->bindMethodCall($method, $define)),
                $priority,
            ),
        );

        return $prototype;
    }

    public function bindImmutableMethod(
        string $method,
        callable|null $define = null,
        int $priority = Priority::NORMAL,
    ): self {
        $prototype          = clone $this;
        $prototype->methods = $prototype->methods->enqueue(
            new ValueAndPriority(
                new ImmutableMethodInterception($this->bindMethodCall($method, $define)),
                $priority,
            ),
        );

        return $prototype;
    }

    public function getConstructor(): CreationalMethod
    {
        return $this->constructor;
    }

    /** @return Map<string, Provider> */
    public function getProperties(): Map
    {
        return $this->properties;
    }

    /** @return Sequence<Interception> */
    public function getMethods(): Sequence
    {
        return ArraySequence::fromArray(
            array_map(static function (ValueAndPriority $method): Interception {
                return $method->getValue();
            }, $this->methods->toArray()),
        );
    }

    private function bindMethodCall(string $method, callable|null $define = null): MethodCall
    {
        $call = new MethodCall($this->class, $method);
        if ($define !== null) {
            $call = $define($call);
        }

        return $call;
    }
}
