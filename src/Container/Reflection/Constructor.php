<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use Vivarium\Assertion\String\IsClass;
use Vivarium\Container\Container;

final class Constructor extends BaseMethod implements CreationalMethod
{
    public function __construct(private string $class)
    {
        (new IsClass())
            ->assert($class);

        parent::__construct('__construct');
    }

    public function invoke(Container $container): mixed
    {
        $reflector = new ReflectionClass($this->class);

        $args = [];
        if ($reflector->hasMethod($this->getName())) {
            $args = $this->getArgumentsValue($this->class, $container)
                         ->toArray();
        }

        return $reflector->newInstanceArgs($args);
    }
}
