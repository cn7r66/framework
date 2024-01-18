<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Comparator;

/** @template T */
final class ValueAndPriority implements Sortable
{
    /** @param T $value */
    public function __construct(private $value, private int $priority = Priority::NORMAL)
    {
    }

    /** @return T */
    public function getValue()
    {
        return $this->value;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
