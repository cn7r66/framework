<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Container\Container;

final class Constructor extends BaseMethod implements StaticMethod
{
    public function __construct(string $class) {
        parent::__construct($class, '__construct');
    }

    public function invoke(Container $container): mixed
    {
        return (new \ReflectionClass($this->getClass()))
            ->newInstanceArgs(
                $this->getArgumentsValue($container)
                     ->toArray()
            );
    }
}
