<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Injection;

use ReflectionClass;
use Vivarium\Comparator\Priority;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\CreationalMethod;

final class StaticMethodCall extends BaseMethod implements CreationalMethod
{
    private string|null $parameter;

    /** @param class-string $class */
    public function __construct(private string $class, string $method, int $priority = Priority::NORMAL)
    {
        parent::__construct($method, $priority);

        $this->parameter = null;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function instanceOn(string $parameter): self
    {
        $method            = clone $this;
        $method->parameter = $parameter;

        return $method;
    }

    public function inject(Container $container, object $instance): object
    {
        $method = $this->parameter === null ?
            $this : $this->bindParameter($this->parameter)
                         ->toInstance($instance);

        $method->invoke($container);

        return $instance;
    }

    public function invoke(Container $container): mixed
    {
        $reflector = (new ReflectionClass($this->class))
            ->getMethod($this->getName());

        return $reflector->invokeArgs(
            null,
            $this->getArgumentsValue($this->class, $container)
                 ->toArray(),
        );
    }
}
