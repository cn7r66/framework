<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use ReflectionClass;
use Vivarium\Container\Exception\ClassNotInstantiable;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class PrototypeStep implements SolverStep
{
    public function solve(Key $key, callable $next): Provider
    {
        if (! class_exists($key->getType())) {
            return $next();
        }

        $reflector = new ReflectionClass($key->getType());
        if (! $reflector->isInstantiable()) {
            throw new ClassNotInstantiable(
                ''
            );
        }
    }
}