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
use Vivarium\Container\Binding;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Type\Type;

final class Factory extends BaseMethod implements Provider
{
    public function __construct(
        private Binding $factory,
        string $method,
    ) {
        parent::__construct($factory->getType(), $method);
    }

    public function provide(Container $container): mixed
    {
        $instance = $container->get($this->factory);

        return (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->invokeArgs(
                $instance,
                $this->getArgumentsValue($container)->toArray(),
            );
    }

    public function getTarget(): string
    {
        $type = (new ReflectionClass($this->getClass()))
            ->getMethod($this->getName())
            ->getReturnType();

        return $type === null ? Type::MIXED : $type->getName();
    }

    public function getCapabilities(): Set
    {
        return HashSet::fromArray([
            Capability::INTERCEPTABLE,
            Capability::DECORABLE,
        ]);
    }
}
