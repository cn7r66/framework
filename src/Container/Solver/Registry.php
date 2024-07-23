<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Solver;

use RuntimeException;
use Vivarium\Assertion\Boolean\IsFalse;
use Vivarium\Assertion\Boolean\IsTrue;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsClass;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsType;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Collection\Set\Set;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\Binder;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\DecoratorBinder;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Binding\InterceptionBinder;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Definition;
use Vivarium\Container\Exception\BindingNotFound;
use Vivarium\Container\Interception;
use Vivarium\Container\Interception\Decorator;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Container\Provider\Interceptor;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Provider\Service;
use Vivarium\Container\Scope;
use Vivarium\Container\RecursiveProvider;
use Vivarium\Container\Solver;
use Vivarium\Equality\Equal;

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

    /** @return Binder<Registry> */
    public function bind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder
    {
        $binding = new TypeBinding($type, $tag, $context);
        
        return new Binder(function (Provider $provider) use ($binding) : Registry {
            $registry            = clone $this;
            $registry->providers = $registry->providers->put($binding, $provider);

            return $registry;
        });
    }

    /**
     * @param class-string                     $class
     * @param non-empty-string                 $tag
     * @param non-empty-string                 $context
     * 
     * @return ProviderBinder<Registry,Definition>
     */
    public function define(
        string $class,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ProviderBinder {
        $binding = new ClassBinding($class, $tag, $context);

        return new ProviderBinder(new Prototype($class), function (Definition $definition) use ($binding): Registry {
            $registry            = clone $this;
            $registry->providers = $registry->providers->put($binding, $definition);

            return $registry;
        });
    }

    /** @return ProviderBinder<Registry,Provider> */
    public function extend(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ProviderBinder {
        $binding = new TypeBinding($type, $tag, $context);

        (new IsTrue())
            ->assert(
                $this->providers->containsKey($binding),
                sprintf("Binding (%s, %s, %s) does not exists.", $type, $tag, $context)
            );
        
        return new ProviderBinder(
            $this->providers->get($binding), 
            function (Provider $provider) use ($binding): Registry {
                $registry = clone $this;
                $registry->providers = $registry->providers->put($binding, $provider);
                
                return $registry;
        });
    }

    /** @return ScopeBinder<Registry> */
    public function scope(
        string $type, 
        string $tag = Binding::DEFAULT, 
        string $context = Binding::GLOBAL
    ): ScopeBinder {
        $binding = new TypeBinding($type, $tag, $context);

        return new ScopeBinder(function (Scope $scope) use ($binding): Registry {
            $registry         = clone $this;
            $registry->scopes = $registry->scopes->put($binding, $scope);

            return $registry;
        });
    }

    /** @return InterceptionBinder<Registry> */
    public function intercept(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): InterceptionBinder {
        $binding = $this->createBinding($type, $tag, $context);

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

    /** @return DecoratorBinder<Registry> */
    public function decorate(
        string $type, 
        string $tag = Binding::DEFAULT, 
        string $context = Binding::GLOBAL
    ): DecoratorBinder
    {
        $binding = new ClassBinding($type, $tag, $context);

        return new DecoratorBinder(function (Decorator $decorator, int $priority) use ($binding): Registry {
            $registry             = clone $this;
            $registry->decorators = $registry->decorators->put(
                $binding,
                new ValueAndPriority(
                    $decorator,
                    $priority
                )
            );

            return $registry;
        });
    }

    public function hasProvider(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): bool
    {
        $binding = new TypeBinding($type, $tag, $context);
        if ($this->providers->containsKey($binding)) {
            return true;
        }

        if ($binding->couldBeWidened()) {
            $binding = $binding->widen();
            return $this->hasProvider(
                $binding->getId(),
                $binding->getTag(),
                $binding->getContext()
            );
        }

        return false;
    }

    public function hasInterceptions(
        string $class, 
        string $tag = Binding::DEFAULT, 
        string $context = Binding::GLOBAL
    ): bool {
        $binding = new ClassBinding($class, $tag, $context);
        foreach ($binding->hierarchy() as $check) {
            if ($this->interceptions->containsKey($check)) {
                return true;
            }
        }

        return false;
    }

    public function hasDecorator(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL
    ) {
        $binding = new ClassBinding($type, $tag, $context);
        if ($binding->couldBeWidened()) {
            $binding = $binding->widen();
            return $this->hasProvider(
                $binding->getId(),
                $binding->getTag(),
                $binding->getContext()
            );
        }

        return false;
    }

    public function hasScope(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): bool
    {
        return $this->scopes->containsKey(
            new TypeBinding($type, $tag, $context)
        );
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
        if (! $provider instanceof Interceptor) {
            $provider = new Interceptor($provider);
        }

        foreach ($this->getHierarchy($request, $provider) as $binding) {
            foreach ($this->interceptions->get($binding) as $interception) {
                $provider = $provider->withInterception(
                    $interception->getValue(),
                    $interception->getPriority(),
                );
            }
        }

        return $provider;
    }

    private function getHierarchy(Binding $request, Provider $provider): Sequence
    {
        if (! $provider instanceof RecursiveProvider) {
            return $request->hierarchy();
        }

        $hierarchy = new ArraySequence();
        if (! Equal::areEquals($request, $provider->getTarget()) && $request->getTag() !== Binding::DEFAULT) {
            $hierarchy = $hierarchy->add($request);
        }

        return $hierarchy;
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
            $this->scopes->get($request) : Scope::TRANSIENT;

        return match ($scope) {
            Scope::SERVICE   => new Service($provider),
            Scope::CLONEABLE => new Cloneable($provider),
            Scope::TRANSIENT => $provider
        };
    }

    private function createBinding(string $type, string $tag, string $context)
    {
        (new Either(
            new IsType(),
            new IsClassOrInterface()
        ))->assert($type);

        return (new IsClassOrInterface)($type) ?
            new ClassBinding($type, $tag, $context) : new TypeBinding($type, $tag, $context);
    }
}
