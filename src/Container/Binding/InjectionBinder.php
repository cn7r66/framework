<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Binding;

use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Assertion\Object\HasProperty;
use Vivarium\Assertion\Type\IsClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Injection;

final class InjectionBinder
{
    public function __construct(private Binding $binding, callable $create)
    {
    }

    public function onProperty(string $property): void
    {
        (new IsClass())
            ->assert($this->binding->getType());

        (new HasProperty($property))
            ->assert($this->binding->getType());
    }

    public function onMethod(string $method): void
    {
        (new IsClass())
            ->assert($this->binding->getType());

        (new HasMethod($method))
            ->assert($this->binding->getType());
    }

    public function onImmutableMethod(string $method): void
    {
        (new IsClass())
            ->assert($this->binding->getType());

        (new HasMethod($method))
            ->assert($this->binding->getType());
    }

    public function withInjection(Injection $injection): void
    {
    }
}
