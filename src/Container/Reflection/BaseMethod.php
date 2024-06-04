<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Vivarium\Assertion\Conditional\NullOr;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binding\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Exception\ParameterNotFound;
use Vivarium\Container\Exception\ParameterNotSolvable;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Fallback;
use Vivarium\Container\Provider\Instance;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseMethod implements Method
{
    /** @var Map<string, Provider> */
    private Map $parameters;

    /** @psalm-assert class-string $class */
    public function __construct(private string $class, private string $method)
    {
        (new HasMethod($method))
            ->assert($class);

        $this->parameters = new HashMap();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->method;
    }

    public function bindParameter(string $parameter): Binder
    {
        return new Binder(function (Provider $provider) use ($parameter): self {
            $method             = clone $this;
            $method->parameters = $method->parameters->put($parameter, $provider);

            return $method;
        });
    }

    public function getParameter(string $parameter): Provider
    {
        if (! $this->hasParameter($parameter)) {
            throw new ParameterNotFound($parameter, $this->method);
        }

        return $this->parameters->get($parameter);
    }

    public function hasParameter(string $parameter): bool
    {
        return $this->parameters->containsKey($parameter);
    }

    /** @return Sequence<Provider> */
    public function getArguments(string|null $class = null): Sequence
    {
        (new NullOr(
            new IsAssignableTo($this->class)
        ))->assert($class);

        $class = $class ?? $this->class;

        $method = (new ReflectionClass($class))
            ->getMethod($this->method);

        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->solveParameter($method, $parameter);
        }

        return ArraySequence::fromArray($arguments);
    }

    public function getArgumentsValue(Container $container, string|null $class = null): Sequence
    {
        $values = [];
        foreach ($this->getArguments($class) as $argument) {
            $values[] = $argument->provide($container);
        }

        return ArraySequence::fromArray($values);
    }

    private function solveParameter(ReflectionMethod $method, ReflectionParameter $parameter): Provider
    {
        if ($this->parameters->containsKey($parameter->getName())) {
            return $this->parameters->get($parameter->getName());
        }

        if ($parameter->hasType()) {
            $binding = new TypeBinding(
                $parameter->isVariadic() ? 'array' : (string) $parameter->getType(),
                Binding::DEFAULT,
                $method->getDeclaringClass()->getName(),
            );

            return $parameter->isOptional() ?
                new Fallback($binding, $parameter->getDefaultValue()) : new ContainerCall($binding);
        }

        if ($parameter->isOptional()) {
            return new Instance(
                $parameter->isVariadic() ? [] : $parameter->getDefaultValue(),
            );
        }

        throw new ParameterNotSolvable($method->getName(), $parameter->getName());
    }

    public function equals(object $object): bool 
    { 
        if (! $object instanceof InstanceMethod) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->getClass(), $object->getClass())
            ->append($this->getName(), $object->getName())
            ->isEquals();
    }

    public function hash(): string 
    {
        return (new HashBuilder())
            ->append($this->getClass())
            ->append($this->getName())
            ->getHashCode();
    }
}
