<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Container\Reflection\InstanceMethod;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseMethodInterception implements MethodInterception
{
    public function __construct(private InstanceMethod $method)
    {
    }

    public function accept(string $type): bool
    {
        return (new IsAssignableTo($type))($this->method->getClass());
    }

    public function getMethodCall(): InstanceMethod
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
            ->append($this->method->getName(), $object->method->getName())
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->method->getName())
            ->getHashCode();
    }
}
