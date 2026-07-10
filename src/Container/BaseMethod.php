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
use Vivarium\Assertion\Numeric\IsInHalfOpenRightRange;
use Vivarium\Assertion\Object\HasParameter;
use Vivarium\Assertion\Object\HasPublicMethod;
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
use Vivarium\Type\Type;

use function count;

abstract class BaseMethod implements Method
{
    /** @var Map<string, Provider> */
    private Map $arguments;

    public function __construct(private string $class, private string $method)
    {
        $reflectionClass = new ReflectionClass($class);
        $isImplicitConstructor = $this->method === '__construct' && ! $reflectionClass->hasMethod($this->method);

        if (! $isImplicitConstructor) {
            (new HasPublicMethod($method))
                ->assert($class);
        }

        $this->arguments = new HashMap();
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getName(): string
    {
        return $this->method;
    }

    public function bindArgument(string $parameter): ProviderBinder
    {
        (new HasParameter($this->class, $this->method))
            ->assert($parameter);

        $binding = new Binding(
            Type::ofMethodParameter($this->class, $this->method, $parameter),
        );

        return new ProviderBinder(
            $binding,
            function (Binding $source, Provider $provider) use ($parameter): static {
                $method            = clone $this;
                $method->arguments = $this->arguments->put($parameter, $provider);

                return $method;
            },
        );
    }

    public function bindArgumentAtPosition(int $position): ProviderBinder
    {
        $parameters = (new ReflectionClass($this->class))
            ->getMethod($this->method)
            ->getParameters();

        (new IsInHalfOpenRightRange(0, count($parameters)))
            ->assert($position, 'Parameter at position %s does not exist.');

        return $this->bindArgument($parameters[$position]->getName());
    }

    public function getArgument(string $parameter): Provider
    {
        if (! $this->hasArgument($parameter)) {
            throw new ParameterNotFound($parameter, $this->method);
        }

        return $this->arguments->get($parameter);
    }

    public function hasArgument(string $parameter): bool
    {
        return $this->arguments->containsKey($parameter);
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
        if ($this->arguments->containsKey($parameter->getName())) {
            return $this->arguments->get($parameter->getName());
        }

        if ($parameter->getType() === null && ! $parameter->isOptional()) {
            throw new ParameterNotSolvable($this->method, $parameter->getName());
        }

        $type = $parameter->isVariadic() ?
            Type::ARRAY : Type::fromReflectionType($parameter->getType());

        $binding = new Binding(
            $type,
            Binding::DEFAULT,
            $method->getDeclaringClass()->getName(),
        );

        if ($parameter->isOptional()) {
            return new Fallback(
                $binding,
                new Instance($parameter->isVariadic() ? [] : $parameter->getDefaultValue()),
            );
        }

        return new ContainerCall($binding);
    }

    public function equals(object $object): bool
    {
        if ($object === $this) {
            return true;
        }

        if ($object::class !== $this::class) {
            return false;
        }

        $other = $object;

        return (new EqualsBuilder())
            ->append($this->class, $other->getClass())
            ->append($this->method, $other->getName())
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
