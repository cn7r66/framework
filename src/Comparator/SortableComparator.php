<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Comparator;

/**
 * @template T of Sortable
 * @template-implements Comparator<T>
 */
final class SortableComparator implements Comparator
{
    /**
     * @param T $first
     * @param T $second
     */
    public function compare($first, $second): int
    {
        return $first->getPriority() - $second->getPriority();
    }

    /**
     * @param T $first
     * @param T $second
     */
    public function __invoke($first, $second): int
    {
        return $this->compare($first, $second);
    }
}