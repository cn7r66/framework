<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use ReflectionMethod;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\CreationalMethod;

final class Factory extends BaseMethod implements CreationalMethod, Provider
{
    private Binding $factory;

    public function __construct(
        string $class,
        string $method,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ) {
        $this->factory = new ClassBinding(
            $class,
            $tag,
            $context,
        );

        parent::__construct($method);
    }

    public function provide(Container $container): mixed
    {
        return $this->invoke($container);
    }

    public function invoke(Container $container): mixed
    {
        $instance = $container->get($this->factory);

        $method = new ReflectionMethod($instance, $this->getName());

        return $method->invokeArgs(
            $instance,
            $this->getArgumentsValue($this->factory->getId(), $container)
                 ->toArray(),
        );
    }
}
