<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseMethodInterception implements MethodInterception
{
    public function __construct(private MethodCall $method)
    {
    }

    public function getMethodCall(): MethodCall
    {
        return $this->method;
    }

    public function configure(callable $configure): self
    {
        $interception         = clone $this;
        $interception->method = $configure($interception->method);

        return $interception;
    }

    public function equals(object $object): bool
    {
        if (! $object instanceof MethodInterception) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->method, $object->method)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->method)
            ->getHashCode();
    }
}
