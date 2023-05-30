<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Installer;

use Vivarium\Container\Solver\SolverStep;

interface Installer
{
    public function withStep(SolverStep $step, int|null $priority = null): Installer;

    /**
     * @param class-string<T> $class
     * @param callable(): T   $factory
     *
     * @template T of SolverStep
     */
    public function withStepFactory(string $class, callable $factory, int $priority): Installer;

    /**
     * @param class-string<T> $class
     *
     * @return T
     *
     * @template T of SolverStep
     */
    public function getStep(string $class);

    /** @return array<SolverStep> */
    public function getSteps(): array;
}