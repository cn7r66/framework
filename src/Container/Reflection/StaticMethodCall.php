<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Comparator\Priority;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Container\Reflection\CreationalMethod;

final class StaticMethodCall extends BaseMethod implements CreationalMethod
{
    private string|null $parameter;

    /** @param class-string $class */
    public function __construct(private string $class, string $method, int $priority = Priority::NORMAL)
    {
        parent::__construct($method, $priority);

        $this->parameter = null;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function invoke(Container $container): mixed
    {
        return $this->getReflector($this->class)
                    ->invokeArgs(
                        null,
                        $this->getArgumentsValue($this->class, $container)
                             ->toArray(),
                    );
    }
}
