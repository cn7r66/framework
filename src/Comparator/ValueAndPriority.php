<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Comparator;

/**
 * @template T
 */
final class ValueAndPriority implements Sortable
{
    /** @var T */
    private $value;

    private int $priority;

    /**
     * @param T $value
     * @param int $priority
     */
    public function __construct($value, int $priority = Priority::NORMAL)
    {
        $this->value    = $value;
        $this->priority = $priority;
    }

    /**
     * @return T
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
