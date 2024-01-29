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
use Vivarium\Container\Reflection\InstanceMethod;

final class PrivateMethodCall extends MethodInjection implements InstanceMethod
{
    public function inject(Container $container, object $instance): object
    {
        $this->invoke($container, $instance);

        return $instance;
    }

    public function invoke(Container $container, object $instance): mixed
    {
        $reflector = (new ReflectionClass($instance::class))
            ->getMethod($this->getName());

        $reflector->setAccessible(true);

        return $reflector->invokeArgs(
            $instance,
            $this->getArgumentsValue($instance::class, $container)
                 ->toArray(),
        );
    }
}
