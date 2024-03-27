<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use Vivarium\Container\Container;

final class StaticMethodCall extends BaseMethod implements CreationalMethod
{
    public function invoke(Container $container): mixed 
    {        
        return (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->invokeArgs(
                null,
                $this->getArgumentsValue($container)
                     ->toArray()   
            );
    }
}
