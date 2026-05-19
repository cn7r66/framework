<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Set\Set;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding\Binder;
use Vivarium\Container\Binding\DecoratorBinder;
use Vivarium\Container\Binding\EnhancementBinder;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Decorator;
use Vivarium\Container\Interception;
use Vivarium\Container\Provider\Constructor;
use Vivarium\Container\Provider\ContainerCall;

final class EagerRegistry implements Registry, Binder
{
    /** @var Map<Binding, Provider> */
    private Map $providers;

    /** @var MultiMap<Binding, SortedSet<ValueAndPriority<Interception>>> */
    private MultiMap $interceptions;

    /** @var MultiMap<Binding, Set<ValueAndPriority<Decorator>>> */
    private MultiMap $decorators;

    /** @var Map<Binding, Scope> */
    private Map $scopes;

    public function __construct()
    {
        $this->providers = new HashMap();

        $this->interceptions = new MultiValueMap(static function (): PriorityQueue {
            return new PriorityQueue(new SortableComparator());
        });
        
        $this->decorators    = new MultiValueMap(static function (): SortedSet {
            return new SortedSet(new SortableComparator());
        });

        $this->scopes = new HashMap();
    }

    /** @return ProviderBinder<EagerRegistry> */
    public function bind(
        string $type, 
        string $tag = Binding::DEFAULT, 
        string $context = Binding::GLOBAL
    ): ProviderBinder
    {
        $binding = new Binding($type, $tag, $context);

        return new ProviderBinder($binding, function (Binding $source, Provider $provider): Registry {
            $registry            = clone $this;
            $registry->providers = $registry->providers->put($source, $provider);

            return $registry;
        });
    }

    /**
     * @param class-string     $class
     * @param non-empty-string $tag
     * @param non-empty-string $context
     *
     * @return ProviderBinder<Registry,Definition>
     */
    public function define(
        string $class,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ScopeBinder 
    {
        $binding = new Binding($class, $tag, $context);


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
                sprintf('Binding (%s, %s, %s) does not exists.', $type, $tag, $context),
            );

        return new ProviderBinder(
            $this->providers->get($binding),
            function (Provider $provider) use ($binding): Registry {
                $registry            = clone $this;
                $registry->providers = $registry->providers->put($binding, $provider);

                return $registry;
            },
        );
    }

    /** @return ScopeBinder<Registry> */
    public function scope(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
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
        string $context = Binding::GLOBAL,
    ): DecoratorBinder {
        $binding = new ClassBinding($type, $tag, $context);

        return new DecoratorBinder(function (Decorator $decorator, int $priority) use ($binding): Registry {
            $registry             = clone $this;
            $registry->decorators = $registry->decorators->put(
                $binding,
                new ValueAndPriority(
                    $decorator,
                    $priority,
                ),
            );

            return $registry;
        });
    }
}
