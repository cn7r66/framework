<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Container;

final class FactoryMethodCall extends BaseMethod implements CreationalMethod
{
    private ClassBinding $factory;

    public function __construct(
        string $class, 
        string $method,         
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL)
    {
        parent::__construct($class, $method);

        $this->factory = new ClassBinding(
            $class,
            $tag,
            $context
        );
    }

    public function getBinding(): ClassBinding
    {
        return $this->factory;
    }

    public function invoke(Container $container): mixed 
    { 
        $this->assertIsAccesible();

        $instance = $container->get($this->factory);

        return (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->invokeArgs(
                $instance,
                $this->getArgumentsValue($container)
                     ->toArray()
            );
    }
}
