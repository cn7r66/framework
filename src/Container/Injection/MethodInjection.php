<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Injection;

use Vivarium\Comparator\Priority;
use Vivarium\Container\Injection;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\Method;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class MethodInjection extends BaseMethod implements Injection
{
    public function __construct(string $method, private int $priority = Priority::NORMAL)
    {
        parent::__construct($method);
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof Method) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->getName(), $object->getName())
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->getName())
            ->getHashCode();
    }
}
