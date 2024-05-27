<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\BaseInterceptable;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\StaticMethodCall;

final class StaticFactory extends InterceptableProvider
{
    private CreationalMethod $method;

    public function __construct(string $class, string $method)
    {
        parent::__construct();
        
        $this->method = new StaticMethodCall($class, $method);
    }

    public function configure(callable $configure): self
    {
        $factory         = clone $this;
        $factory->method = $configure($factory->method);

        return $factory;
    }

    protected function provideInstance(Container $container): object 
    { 
        return $this->method->invoke($container);
    }
}
