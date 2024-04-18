<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Step;

use RuntimeException;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\GenericInterceptor;
use Vivarium\Container\Interception;
use Vivarium\Container\Interceptor;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;

final class Solver implements ConfigurableSolver
{
    private Map $providers;

    public function __construct()
    {
        $this->providers = new HashMap();
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

        return new GenericBinder(function (Provider $provider) use ($binding) {
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

    public function extend(string $type, callable $extend, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): self { }

    public function intercept(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Interceptor 
    { 
        $binding = new TypeBinding($type, $tag, $context);

        return new GenericInterceptor(function (Interception $interception) use ($binding) {
            return $interception;
        });
    }

    public function decorate(): self { }

    public function solve(Binding $request, callable $next): Provider
    {
        // TODO: Implement solve() method.
    }
}
