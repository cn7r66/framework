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
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\FactoryMethodCall;

final class Factory extends InterceptableProvider
{
    private CreationalMethod $method;

    public function __construct(
        string $class,
        string $method,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ) {
        parent::__construct();

        $this->method = new FactoryMethodCall(
            $class,
            $method,
            $tag,
            $context,
        );
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
