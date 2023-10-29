<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Comparator\IntegerComparator;
use Vivarium\Container\Container;
use Vivarium\Container\Injection;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Injector implements Provider
{
    /** Queue<Injection> */
    private Queue $injections;

    public function __construct(private Provider $provider)
    {
        $this->injections = new PriorityQueue(new IntegerComparator());
    }

    public function provide(Container $container): mixed
    {
        $instance = $this->provider->provide($container);
        foreach ($this->injections as $injection) {
            $instance = $injection->inject($container, $instance);
        }

        return $instance;
    }

    public function getKey(): Key
    {
        return $this->provider->getKey();
    }

    public function withInjection(Injection $injection, int $priority): self
    {
        $injector             = clone $this;
        $injector->injections = $injector->injections->enqueue($injection);

        return $injector;
    }

    /**
     * @return Queue<Injection>
     */
    public function getInjections(): Queue
    {
        return $this->injections;
    }

    public function merge(Injector $injector): self
    {

    }
}
