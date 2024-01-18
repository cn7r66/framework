<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Step;

use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Step;

final class ReflectionSolver implements Step
{
    public function solve(Binding $request, callable $next): Provider
    {
        try {
            $binding   = ClassBinding::fromBinding($request);
            $reflector = new ReflectionClass($binding->getId());
            if (! $reflector->isInstantiable()) {
                return $next();
            }

            return new Prototype($binding->getId());
        } catch (ReflectionException) {
            throw new RuntimeException();
        }
    }
}
