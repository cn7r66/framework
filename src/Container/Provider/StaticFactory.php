<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Provider;

use ReflectionClass;
use Vivarium\Collection\Set\HashSet;
use Vivarium\Collection\Set\Set;
use Vivarium\Container\BaseMethod;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;

final class StaticFactory extends BaseMethod implements Provider
{
    public function __construct(string $class, string $method)
    {
        parent::__construct($class, $method);
    }

    public function provide(Container $container): mixed
    {
        return (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->invokeArgs(
                null,
                $this->getArgumentsValue($container)->toArray(),
            );
    }

    public function getTarget(): string
    {
        $type = (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->getReturnType();

        return $type === null ? 'mixed' : (string) $type;
    }

    public function getCapabilities(): Set
    {
        return HashSet::fromArray([
            Capability::INTERCEPTABLE,
            Capability::DECORABLE,
        ]);
    }
}
