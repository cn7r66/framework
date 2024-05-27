<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Set\Set;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\Priority;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Container;
use Vivarium\Container\Interceptable;
use Vivarium\Container\Interception;
use Vivarium\Container\Provider;

abstract class InterceptableProvider implements Interceptable, Provider
{
    /** @var Set<ValueAndPriority<Interception>> */
    private Set $interceptions;

    public function __construct()
    {
        $this->interceptions = new SortedSet(
            new SortableComparator()
        );
    }

    public function withInterception(Interception $interception, int $priority = Priority::NORMAL): self
    {
        $interception = new ValueAndPriority(
            $interception,
            $priority
        );

        $interceptable = clone $this;
        $interceptable->interceptions = $interceptable->interceptions->remove($interception)
                                                                     ->add($interception);
        
        return $interceptable;
    }

    public function provide(Container $container): mixed
    {
        $instance = $this->provideInstance($container);
        foreach ($this->interceptions as $interception) {
            $instance = $interception->getValue()
                                     ->intercept($container, $instance);
        }

        return $instance;
    }

    protected abstract function provideInstance(Container $container) : object;
}
