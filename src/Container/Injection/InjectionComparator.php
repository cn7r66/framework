<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Injection;

use Vivarium\Comparator\Comparator;
use Vivarium\Container\Injection;

/** @template-implements Comparator<Injection> */
final class InjectionComparator implements Comparator
{
    /**
     * @param Injection $first
     * @param Injection $second
     */
    public function compare($first, $second): int
    {
        return $first->getPriority() - $second->getPriority();
    }

    public function __invoke($first, $second): int
    {
        return $this->compare($first, $second);
    }
}
