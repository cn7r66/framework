<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Provider;

use Closure;
use ReflectionFunction;
use Vivarium\Assertion\Comparison\IsEqualsTo;
use Vivarium\Assertion\Type\IsAssignableTo;
use Vivarium\Assertion\Var\IsFunction;
use Vivarium\Collection\Set\HashSet;
use Vivarium\Collection\Set\Set;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Type\Type;

use function count;

final class Fun implements Provider
{
    /** @param callable(Container):mixed $fn */
    public function __construct(private Closure|string $fn)
    {
        (new IsFunction())
            ->assert($fn);

        $parameters = (new ReflectionFunction($fn))
            ->getParameters();

        (new IsEqualsTo(1))
            ->assert(count($parameters));

        (new IsAssignableTo((string) $parameters[0]->getType()))
            ->assert(Container::class);
    }

    public function provide(Container $container): mixed
    {
        return ($this->fn)($container);
    }

    public function getTarget(): string
    {
        return Type::ofFunction($this->fn);
    }

    public function getCapabilities(): Set
    {
        return HashSet::fromArray([
            Capability::INTERCEPTABLE,
            Capability::DECORABLE,
        ]);
    }
}
