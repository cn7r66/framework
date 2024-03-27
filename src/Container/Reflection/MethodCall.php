<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Container\Container;

final class MethodCall extends BaseMethod implements InstanceMethod
{
    public function invoke(Container $container, object $instance): mixed
    {
        (new IsAssignableTo($this->getClass()))
            ->assert($instance::class);

        return (new ReflectionClass($instance::class))
            ->getMethod($this->getName())
            ->invokeArgs(
                $instance,
                $this->getArgumentsValue($container)
                     ->toArray(),
            );
    }
}
