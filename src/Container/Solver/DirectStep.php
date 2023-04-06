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

final class DirectStep implements SolverStep
{
    /** @var Registry<callable(Key):Provider> */
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function solve(Key $key, callable $next): Provider
    {
        if (! $this->registry->has($key)) {
            return $next();
        }

        return $this->registry->get($key)($key);
    }

    /**
     * @param Key                     $key
     * @param callable(Key): Provider $solver
     *
     * @return $this
     */
    public function withSolver(Key $key, callable $solver): self
    {
        $direct = clone $this;
        $direct->registry = $direct->registry
            ->add($key, $solver);

        return $direct;
    }
}
