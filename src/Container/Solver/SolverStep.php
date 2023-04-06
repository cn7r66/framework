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

interface SolverStep
{
    /**
     * @param Key      $key
     * @param callable():Provider $next
     *
     * @return Provider
     */
    public function solve(Key $key, callable $next): Provider;
}
