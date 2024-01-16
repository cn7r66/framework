<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Container\Binding;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver;
use Vivarium\Container\Step;

final class LazySolver implements Step
{
    private Step $solver;

    /** @var callable():Step */
    private $init;

    /**
     * @param callable():iterable<Module> $modules;
     */
    public function __construct(callable $modules, ConfigurableSolver|null $solver = null)
    {
        $this->solver = null;
        $this->init   = function () use ($modules, $solver) {
            $solver = $solver === null ? new Solver() : $solver;
            foreach ($modules() as $module) {
                $solver = $module->install($solver);
            }

            return $solver;
        };
    }

    public function solve(Binding $request, callable $next): Provider
    {
        if ($this->solver === null) {
            $this->solver = ($this->init)();
        }

        return $this->solver->solve($request, $next);
    }
}