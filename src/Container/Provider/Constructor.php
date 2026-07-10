<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Provider;

use ReflectionClass;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Collection\Set\HashSet;
use Vivarium\Collection\Set\Set;
use Vivarium\Container\BaseMethod;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Method;
use Vivarium\Container\Provider;

final class Constructor extends BaseMethod implements Provider
{
    public function __construct(string $class)
    {
        parent::__construct($class, Method::CONSTRUCT);
    }

    public function getArguments(string|null $class = null): Sequence
    {
        if (! (new ReflectionClass($this->getClass()))->hasMethod(Method::CONSTRUCT)) {
            return ArraySequence::fromArray([]);
        }

        return parent::getArguments($class);
    }

    public function provide(Container $container): mixed
    {
        return (new ReflectionClass($this->getClass()))
            ->newInstanceArgs(
                $this->getArgumentsValue($container)->toArray(),
            );
    }

    public function getTarget(): string
    {
        return $this->getClass();
    }

    public function getCapabilities(): Set
    {
        return HashSet::fromArray([
            Capability::INJECTABLE,
            Capability::INTERCEPTABLE,
            Capability::DECORABLE,
        ]);
    }
}
