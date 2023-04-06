<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Comparator\Comparator;
use Vivarium\Container\Installer\StepAndPriority;

/**
 * @template-implements Comparator<StepAndPriority>
 */
final class StepAndPriorityComparator implements Comparator
{
    public function compare($first, $second): int
    {
        return $first->getPriority() - $second->getPriority();
    }

    public function __invoke($first, $second): int
    {
        return $this->compare($first, $second);
    }
}
