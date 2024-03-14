<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\StaticMethodCall;

final class StaticFactory implements Provider
{
    private CreationalMethod $method;

    public function __construct(string $class, string $method) 
    {
        $this->method = new StaticMethodCall($class, $method);
    }
    
    public function provide(Container $container): mixed 
    { 
        return $this->method->invoke($container);
    }
}