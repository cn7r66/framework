<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Provider\Prototype;

final class Solver implements ConfigurableSolver
{
    private Map $providers;

    public function __construct()
    {
        $this->providers = new HashMap();
    }

    public function bind(string $type, string $tag, string $context): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);

        if ($this->providers->containsKey($binding)) {
            throw new \RuntimeException();
        }

        return $this->rebind($type, $tag, $context);
    }

    public function rebind(string $type, string $tag, string $context): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);

        return new GenericBinder(function (Provider $provider) use ($binding) {
            $solver = clone $this;
            $solver->providers = $solver->providers->put($binding, $provider);

            return $solver;
        });
    }

    /**
     * @param class-string $class
     * @param callable(Definition): Definition $define
     * @param non-empty-string $tag
     * @param non-empty-string $context
     */
    public function define(string $class, callable $define, string $tag, string $context): self
    {
        $binding = new ClassBinding($class, $tag, $context);
        if ($this->providers->containsKey($binding)) {
            throw new \RuntimeException();
        }

        $solver = clone $this;
        $solver->providers = $solver->providers->put(
            $binding,
            $define(new Prototype($class))
        );

        return $solver;
    }

    public function inject(string $type, callable $inject, string $tag, string $context): self
    {
        // TODO: Implement inject() method.
    }

    public function extend(string $type, callable $extend, string $tag, string $context): self
    {
        // TODO: Implement extend() method.
    }

    public function decorate()
    {
        // TODO: Implement decorate() method.
    }

    public function solve(Binding $request, callable $next): Provider
    {
        // TODO: Implement solve() method.
    }
}
