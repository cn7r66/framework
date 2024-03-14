<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Assertion\Object\HasMethod;
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
        parent::__construct($method);

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
        $instance = $container->get($this->factory);

        return $this->getReflector($this->factory->getId())
                    ->invokeArgs(
                        $instance,
                        $this->getArgumentsValue($this->factory->getId(), $container)
                             ->toArray()
                    );
    }
}
