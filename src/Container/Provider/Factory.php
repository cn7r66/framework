<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Binding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\FactoryMethodCall;

final class Factory implements Provider
{
    private CreationalMethod $method;

    public function __construct(
        string $class,
        string $method,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ) {
        $this->method = new FactoryMethodCall(
            $class,
            $method,
            $tag,
            $context
        );
    }

    public function configure(callable $configure): self
    {
        $factory         = clone $this;
        $factory->method = $configure($factory->method);

        return $factory;
    }

    public function provide(Container $container): mixed 
    { 
        return $this->method->invoke($container);
    }
}
