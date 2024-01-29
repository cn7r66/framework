<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use ReflectionClass;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Provider\Prototype;

use function class_exists;

final class ReflectionSolver implements Step
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
