<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use ReflectionClass;
use RuntimeException;
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
use Vivarium\Container\Container;
use Vivarium\Container\Definition;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\Injection;
use Vivarium\Container\Injection\ImmutableMethodCall;
use Vivarium\Container\Injection\MethodCall;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\Constructor;
use Vivarium\Container\Reflection\Method;
use Vivarium\Container\Reflection\StaticMethod;

use function array_map;

final class Prototype implements Definition
{
    private StaticMethod $constructor;

    /** @var Map<string, Provider> */
    private Map $properties;

    /** @var Queue<ValueAndPriority<Injection> */
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
                throw new RuntimeException("");
            }

            $reflector->getProperty($property)
                      ->setValue($instance, $provider->provide($container));
        }

        return $instance;
    }

    public function bindConstructorFactory(string $class, string $method, string $tag, string $context): self
    {
        $prototype              = clone $this;
        $prototype->constructor = new Factory($class, $method, $tag, $context);

        return $prototype;
    }

    public function bindConstructorStaticFactory(string $class, string $method, string $tag, string $context): self
    {
        throw new RuntimeException('Not implemented yet.');
    }

    /** @return Binder<Prototype> */
    public function bindParameter(string $parameter): Binder
    {
        return new GenericBinder(function (Provider $provider) use ($parameter) {
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
        return new GenericBinder(function (Provider $provider) use ($property) {
            $prototype             = clone $this;
            $prototype->properties = $prototype->properties->put($property, $provider);

            return $prototype;
        });
    }

    /** @param callable(Method):Method|null $define */
    public function bindMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL): self
    {
        $call = new MethodCall($this->class, $method);
        if ($define !== null) {
            $call = $define($call);
        }

        $prototype          = clone $this;
        $prototype->methods = $prototype->methods->enqueue(
            new ValueAndPriority(
                $call,
                $priority,
            ),
        );

        return $prototype;
    }

    public function bindImmutableMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL): self
    {
        $call = new ImmutableMethodCall($this->class, $method);
        if ($define !== null) {
            $call = $define($call);
        }

        $prototype          = clone $this;
        $prototype->methods = $prototype->methods->enqueue(
            new ValueAndPriority(
                $call,
                $priority,
            ),
        );

        return $prototype;
    }

    public function getConstructor(): StaticMethod
    {
        return $this->constructor;
    }

    /** @return Map<string, Provider> */
    public function getProperties(): Map
    {
        return $this->properties;
    }

    /** @return Sequence<Method> */
    public function getMethods(): Sequence
    {
        return ArraySequence::fromArray(
            array_map(static function (ValueAndPriority $method): Method {
                return $method->getValue();
            }, $this->methods->toArray()),
        );
    }
}
