<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Collection\Set\Set;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\Priority;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Container;
use Vivarium\Container\Interception;
use Vivarium\Container\Provider;

final class Interceptor implements Provider
{
    /** Set<ValueAndPriority<Interception>> */
    private Set $interceptions;

    public function __construct(private Provider $provider)
    {
        $this->interceptions = new SortedSet(new SortableComparator());
    }

    public function withInterception(Interception $interception, int $priority = Priority::NORMAL): self
    {
        $interception = new ValueAndPriority($interception, $priority);

        $interceptor                = clone $this;
        $interceptor->interceptions = $interceptor->interceptions->add($interception);

        return $interceptor;
    }

    /** @return Sequence<Interception> */
    public function getInterceptions(): Sequence
    {
        $sequence = new ArraySequence();
        foreach ($this->interceptions as $interception) {
            $sequence = $sequence->add($interception->getValue());
        }

        return $sequence;
    }

    public function provide(Container $container): mixed
    {
        $instance = $this->provider->provide($container);
        foreach ($this->interceptions as $interception) {
            $instance = $interception->getValue()->intercept($container, $instance);
        }

        return $instance;
    }
}
