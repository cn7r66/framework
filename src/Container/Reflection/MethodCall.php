<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Container\Container;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\InstanceMethod;

final class MethodCall extends BaseMethod implements InstanceMethod
{
    public function invoke(Container $container, object $instance): mixed
    {
        return $this->getReflector($instance::class)
                    ->invokeArgs(
                        $instance,
                        $this->getArgumentsValue($instance::class, $container)
                             ->toArray(),
                    );
    }
}
