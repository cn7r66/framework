<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Solver;

use ReflectionClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Solver;

use function class_exists;

final class Reflector implements Solver
{
    public function solve(Binding $request, callable $next): Provider
    {
        if (! class_exists($request->getId())) {
            return $next();
        }

        $binding   = ClassBinding::fromBinding($request);
        $reflector = new ReflectionClass($binding->getId());
        if (! $reflector->isInstantiable()) {
            return $next();
        }

        return new Prototype($binding->getId());
    }
}
