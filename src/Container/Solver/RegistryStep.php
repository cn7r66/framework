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

final class RegistryStep implements SolverStep
{
    /** @var Registry<callable(Key): Provider> */
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function solve(Key $key, callable $next): Provider
    {
        if (! $this->registry->has($key)) {
            return $next($key);
        }

        return $this->registry
            ->get($key)($key);
    }

    public function addSolver(Key $key, callable $solver): self
    {
        $registry = clone $this;
        $registry->registry = $registry->registry
            ->add($key, $solver);

        return $registry;
    }
}
