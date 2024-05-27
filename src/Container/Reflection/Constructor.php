<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use ReflectionClass;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Container;

final class Constructor extends BaseMethod implements CreationalMethod
{
    public function __construct(string $class)
    {
        parent::__construct($class, '__construct');
    }

    public function getArguments(string|null $class = null): Sequence
    {
        $reflector = new ReflectionClass($this->getClass());
        if (! $reflector->hasMethod($this->getName())) {
            return new ArraySequence();
        }

        return parent::getArguments();
    }

    public function invoke(Container $container): object
    {
        return (new ReflectionClass($this->getClass()))
            ->newInstanceArgs(
                $this->getArgumentsValue($container)
                     ->toArray(),
            );
    }
}
