<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver\SolverStep;

final class StepAndPriority
{
    private SolverStep $step;

    private int $priority;

    public function __construct(SolverStep $step, int $priority)
    {
        $this->step     = $step;
        $this->priority = $priority;
    }

    public function getStep(): SolverStep
    {
        return $this->step;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
