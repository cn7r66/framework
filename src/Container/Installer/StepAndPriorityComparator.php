<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Comparator\Comparator;

/** @template-implements Comparator<StepAndPriority> */
final class StepAndPriorityComparator implements Comparator
{
    /**
     * @param StepAndPriority $first
     * @param StepAndPriority $second
     */
    public function compare($first, $second): int
    {
        return $first->getPriority() - $second->getPriority();
    }

    /**
     * @param StepAndPriority $first
     * @param StepAndPriority $second
     */
    public function __invoke($first, $second): int
    {
        return $this->compare($first, $second);
    }
}
