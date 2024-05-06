<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Solver;

use RuntimeException;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Definition;
use Vivarium\Container\Interception;
use Vivarium\Container\Interceptor;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Solver;

final class Registry implements Solver
{
    /** @var Map<Binding, Provider> */
    private Map $providers;

    /** @var MultiMap<Binding, PriorityQueue<ValueAndPriority<Interception>>> */
    private MultiMap $interceptions;

    public function __construct()
    {
        $this->providers     = new HashMap();
        $this->interceptions = new MultiValueMap(static function (): PriorityQueue {
            return new PriorityQueue(new SortableComparator());
        });
    }

    public function bind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);

        if ($this->providers->containsKey($binding)) {
            throw new RuntimeException();
        }

        return $this->rebind($type, $tag, $context);
    }

    public function rebind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);

        return new Binder(function (Provider $provider) use ($binding) {
            $solver            = clone $this;
            $solver->providers = $solver->providers->put($binding, $provider);

            return $solver;
        });
    }

    /**
     * @param class-string                     $class
     * @param callable(Definition): Definition $define
     * @param non-empty-string                 $tag
     * @param non-empty-string                 $context
     */
    public function define(string $class, callable $define, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): self
    {
        $binding = new ClassBinding($class, $tag, $context);
        if ($this->providers->containsKey($binding)) {
            throw new RuntimeException();
        }

        $solver            = clone $this;
        $solver->providers = $solver->providers->put(
            $binding,
            $define(new Prototype($class)),
        );

        return $solver;
    }

    public function extend(string $type, callable $extend, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): self
    {
    }

    public function intercept(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): Interceptor {
        $binding = new ClassBinding($type, $tag, $context);

        return new Interceptor(
            $binding->getId(),
            function (Interception $interception, int $priority) use ($binding): Registry {
                $registry                = clone $this;
                $registry->interceptions = $registry->interceptions->put(
                    $binding,
                    new ValueAndPriority(
                        $interception,
                        $priority,
                    ),
                );

                return $registry;
            },
        );
    }

    public function decorate(): self
    {
    }

    public function solve(Binding $request, callable $next): Provider
    {
        // TODO: Implement solve() method.
    }
}
