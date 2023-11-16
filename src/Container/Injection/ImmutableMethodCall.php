<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Injection;

use ReflectionClass;
use Vivarium\Container\Container;
use Vivarium\Container\Injection;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\InstanceMethod;

final class ImmutableMethodCall extends BaseMethod implements InstanceMethod, Injection
{
    public function __construct(string $class,string $method)
    {
        parent::__construct($class, $method);

        // TODO Assert method is public
        // TODO Assert method return assignable object
    }

    public function inject(Container $container, object $instance): object
    {
        return $this->invoke($container, $instance);
    }

    public function invoke(Container $container, object $instance): mixed
    {
        $reflector = (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName());

        return $reflector->invokeArgs(
            $instance,
            $this->getArgumentsValue($container)
                 ->toArray(),
        );
    }
}
