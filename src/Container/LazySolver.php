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

/** 
 * @template T of Solver
 * @template-implements Solver<T>
 */
final class LazySolver implements Solver
{
    /** @var T */
    private $solver;

    /** @var callable():T */
    private $init;

    /** 
     * @param callable():iterable<Module<T>> $modules; 
     * @param T                              $solver 
     */
    public function __construct(callable $modules, $solver)
    {
        $this->solver = null;
        $this->init   = static function () use ($modules, $solver) {
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
