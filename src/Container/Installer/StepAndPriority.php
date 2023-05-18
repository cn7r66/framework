<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Solver\SolverStep;

final class StepAndPriority
{
    public function __construct(private SolverStep $step, private int $priority)
    {
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
