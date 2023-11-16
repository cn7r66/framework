<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use ReflectionParameter;
use RuntimeException;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binder;
use Vivarium\Container\Container;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\Provider;

abstract class BaseMethod implements Method
{
    /** @var Map<string, Provider> */
    private Map $parameters;

    /** @psalm-assert class-string $class */
    public function __construct(private string $class, private string $method)
    {
        (new IsClassOrInterface())
            ->assert($class);

        (new HasMethod($class))
            ->assert($method);

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
        return new GenericBinder(function (Provider $provider) use ($parameter) {
            $method             = clone $this;
            $method->parameters = $method->parameters->put($parameter, $provider);

            return $method;
        });
    }

    public function getParameter(string $parameter): Provider
    {
        if (! $this->hasParameter($parameter)) {
            throw new RuntimeException();
        }

        return $this->parameters->get($parameter);
    }

    public function hasParameter(string $parameter): bool
    {
        return $this->parameters->containsKey($parameter);
    }

    /** @return Sequence<Provider> */
    public function getArguments(): Sequence
    {
        $method = (new ReflectionClass($this->class))
            ->getMethod($this->method);

        $arguments = [];
        foreach ($method->getParameters() as $parameter) {
            $arguments[] = $this->parameters->containsKey($parameter->getName()) ?
                $this->parameters->get($parameter->getName()) : $this->solveParameter($parameter);
        }

        return ArraySequence::fromArray($arguments);
    }

    public function getArgumentsValue(Container $container): Sequence
    {
        $values = [];
        foreach ($this->getArguments() as $argument) {
            $values[] = $argument->provide($container, $this->class);
        }

        return ArraySequence::fromArray($values);
    }

    private function solveParameter(ReflectionParameter $parameter): Provider
    {
        throw new RuntimeException('Not implemented yet.');
    }
}
