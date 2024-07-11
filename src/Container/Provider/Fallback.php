<?php

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Psr\Container\NotFoundExceptionInterface;
use Vivarium\Assertion\String\IsPrimitive;
use Vivarium\Container\Binding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\RecursiveProvider;

use function gettype;

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

final class Fallback implements RecursiveProvider
{
    public function __construct(private Binding $binding, private mixed $value)
    {
        (new IsPrimitive())
            ->assert(gettype($value));
    }

    public function provide(Container $container): mixed
    {
        try {
            return $container->get($this->binding);
        } catch (NotFoundExceptionInterface) {
            return $this->value;
        }
    }

    public function getTarget(): Binding
    {
        return $this->binding;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
