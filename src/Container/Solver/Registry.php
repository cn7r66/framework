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
use Vivarium\Collection\Set\Set;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\Binder;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\InterceptionBinder;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Definition;
use Vivarium\Container\Interception;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Container\Provider\Interceptor;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Provider\Service;
use Vivarium\Container\Scope;
use Vivarium\Container\Skippable;
use Vivarium\Container\Solver;

final class Registry implements Solver
{
    /** @var Map<Binding, Provider> */
    private Map $providers;

    /** @var MultiMap<Binding, SortedSet<ValueAndPriority<Interception>>> */
    private MultiMap $interceptions;

    /** @var MultiMap<Binding, Set<ValueAndPriprity<Decorator>>> */
    private MultiMap $decorators;

    /** @var Map<Binding, Scope> */
    private Map $scopes;

    public function __construct()
    {
        $this->providers     = new HashMap();
        $this->interceptions = new MultiValueMap(static function (): PriorityQueue {
            return new PriorityQueue(new SortableComparator());
        });
        $this->decorators    = new MultiValueMap(static function (): SortedSet {
            return new SortedSet(new SortableComparator());
        });

        $this->scopes = new HashMap();
    }

    public function bind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);
        if ($this->providers->containsKey($binding)) {
            throw new RuntimeException('Not implemented yet.');
        }

        return new Binder(function (Provider $provider) use ($binding) {
            return new ScopeBinder(function (Scope $scope) use ($binding, $provider) {
                $registry            = clone $this;
                $registry->providers = $registry->providers->put($binding, $provider);
                $registry->scopes    = $registry->scopes->put($binding, $scope);

                return $registry;
            });
        });
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
    public function define(
        string $class,
        callable $define,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): self {
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

    public function extend(
        string $type,
        callable $extend,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): self {
    }

    public function intercept(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): InterceptionBinder {
        $binding = new ClassBinding($type, $tag, $context);

        return new InterceptionBinder(
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

    /** @return Decorator<Registry> */
    public function decorate(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Decorator
    {
        $binding = new ClassBinding($type, $tag, $context);
    }

    public function solve(Binding $request, callable $next): Provider
    {
        $provider = $this->providers->containsKey($request) ?
            $this->providers->get($request) : $next();

        $provider = $this->applyInterceptions($request, $provider);
        $provider = $this->applyDecorator($request, $provider);
        $provider = $this->applyScope($request, $provider);

        return $provider;
    }

    private function applyInterceptions(Binding $request, Provider $provider): Provider
    {
        if ($provider instanceof Skippable) {
            return $provider;
        }

        if (! $provider instanceof Interceptor) {
            $provider = new Interceptor($provider);
        }

        $hierarchy = $request->hierarchy();
        foreach ($hierarchy as $binding) {
            foreach ($this->interceptions->get($binding) as $interception) {
                $provider = $provider->withInterception(
                    $interception->getValue(),
                    $interception->getPriority(),
                );
            }
        }

        return $provider;
    }

    private function applyDecorator(Binding $request, Provider $provider): Provider
    {
        if (! $this->decorators->containsKey($request)) {
            return $provider;
        }

        $provider = new Interceptor($provider);

        foreach ($this->decorators->get($request) as $decorator) {
            $provider = $provider->withInterception(
                $decorator->getValue(),
                $decorator->getPriority(),
            );

            $this->applyInterceptions($request, $provider);
        }

        return $provider;
    }

    private function applyScope(Binding $request, Provider $provider): Provider
    {
        $scope = $this->scopes->containsKey($request) ?
            $this->scopes->get($request) : Scope::PROTOTYPE;

        return match ($scope) {
            Scope::SERVICE   => new Service($provider),
            Scope::CLONEABLE => new Cloneable($provider),
            Scope::PROTOTYPE => $provider
        };
    }
}
