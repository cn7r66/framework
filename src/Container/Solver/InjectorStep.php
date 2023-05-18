<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Solver;

use RuntimeException;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class InjectorStep implements SolverStep
{
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function solve(Key $key, callable $next): Provider
    {
        throw new RuntimeException('Not implemented yet.');
    }
}
