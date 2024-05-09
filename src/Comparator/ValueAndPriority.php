<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Comparator;

use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

/** @template T */
final class ValueAndPriority implements Sortable, Equality
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

    public function equals(object $object): bool 
    { 
        if (! $object instanceof ValueAndPriority) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->value, $object->getValue())
            ->isEquals();
    }

    public function hash(): string 
    { 
        return (new HashBuilder())
            ->append($this->value)
            ->getHashCode();
    }
}
