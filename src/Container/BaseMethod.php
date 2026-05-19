<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Vivarium\Assertion\Conditional\NullOr;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Assertion\Type\IsAssignableTo;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Exception\ParameterNotFound;
use Vivarium\Container\Exception\ParameterNotSolvable;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Fallback;
use Vivarium\Container\Provider\Instance;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseMethod implements Method
{
    /** @var Map<string, Provider> */
    private Map $parameters;

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

    public function bindArgument(string $name): ProviderBinder
    {
        $reflectionParams = (new ReflectionClass($this->class))
            ->getMethod($this->method)
            ->getParameters();

        $paramType = null;
        foreach ($reflectionParams as $parameter) {
            if ($parameter->getName() === $name) {
                $paramType = $parameter->hasType()
                    ? (string) $parameter->getType()
                    : 'mixed';
                break;
            }
        }

        if ($paramType === null) {
            throw new ParameterNotFound($name, $this->method);
        }

        return new ProviderBinder(
            new Binding($paramType),
            function (Binding $source, Provider $provider) use ($name): static {
                $method             = clone $this;
                $method->parameters = $this->parameters->put($name, $provider);

                return $method;
            },
        );
    }

    public function bindArgumentAtPosition(int $position): ProviderBinder
    {
        $parameter = (new ReflectionClass($this->class))
            ->getMethod($this->method)
            ->getParameters()[$position];

        return $this->bindArgument($parameter->getName());
    }

    public function getArgument(string $name): Provider
    {
        if (! $this->hasArgument($name)) {
            throw new ParameterNotFound($name, $this->method);
        }

        return $this->parameters->get($name);
    }

    public function hasArgument(string $name): bool
    {
        return $this->parameters->containsKey($name);
    }

    /** @return Sequence<Provider> */
    public function getArguments(string|null $class = null): Sequence
    {
        (new NullOr(
            new IsAssignableTo($this->class),
        ))->assert($class);

        $class ??= $this->class;

        $method = (new ReflectionClass($class))
            ->getMethod($this->method);

        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->solveParameter($method, $parameter);
        }

        return ArraySequence::fromArray($arguments);
    }

    /** @return Sequence<mixed> */
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
            $binding = new Binding(
                $parameter->isVariadic() ? 'array' : (string) $parameter->getType(),
                Binding::DEFAULT,
                $method->getDeclaringClass()->getName(),
            );

            return $parameter->isOptional()
                ? new Fallback($binding, new Instance($parameter->getDefaultValue()))
                : new ContainerCall($binding);
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
        if (! $object instanceof Method) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->class, $object->getClass())
            ->append($this->method, $object->getName())
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->class)
            ->append($this->method)
            ->getHashCode();
    }
}
