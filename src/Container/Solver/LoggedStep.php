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

final class LoggedStep implements SolverStep
{
    public function __construct()
    {
    }

    public function solve(Key $key, callable $next): Provider
    {
        return $next();
    }
}
