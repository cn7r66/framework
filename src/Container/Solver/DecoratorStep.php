<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class DecoratorStep implements SolverStep
{
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function solve(Key $key, callable $next): Provider
    {
        throw new \RuntimeException('Not implemented yet.');
    }
}
