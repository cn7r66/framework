<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding\Binder;
use Vivarium\Container\Binding\DecoratorBinder;
use Vivarium\Container\Binding\EnhancementBinder;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Provider\ContainerCall;

final class EagerRegistry implements Registry, Binder
{
    /** @var HashMap<string, Provider> */
    private HashMap $providers;

    /** @var HashMap<string, Binding> */
    private HashMap $chains;

    /** @var HashMap<string, Scope> */
    private HashMap $scopes;

    /** @var MultiMap<string, ValueAndPriority<Enhancement>> */
    private MultiMap $enhancements;

    public function __construct()
    {
        $this->providers    = new HashMap();
        $this->chains       = new HashMap();
        $this->scopes       = new HashMap();
        $this->enhancements = new MultiValueMap(function (): PriorityQueue {
            return new PriorityQueue(new SortableComparator());
        });
    }

    public function withProvider(Binding $source, Provider $provider): self
    {
        $registry            = clone $this;
        $registry->providers = $registry->providers->put($source->hash(), $provider);

        return $registry;
    }

    public function withChain(Binding $source, Binding $target): self
    {
        $registry         = clone $this;
        $registry->chains = $registry->chains->put($source->hash(), $target);

        return $registry;
    }

    public function withScope(Binding $binding, Scope $scope): self
    {
        $registry         = clone $this;
        $registry->scopes = $registry->scopes->put($binding->hash(), $scope);

        return $registry;
    }

    public function withEnhancement(Binding $target, Enhancement $enhancement, int $priority): self
    {
        $registry               = clone $this;
        $registry->enhancements = $registry->enhancements->put(
            $target->hash(),
            new ValueAndPriority($enhancement, $priority)
        );

        return $registry;
    }

    public function install(Module $module): self
    {
        $binder = $module->configure($this);

        assert($binder instanceof self);

        return $binder;
    }

    // Binder

    public function bind(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ProviderBinder {
        $binding = new Binding($type, $tag, $context);

        return new ProviderBinder(
            function (Binding $b, Provider $provider): ScopeBinder {
                return new ScopeBinder(
                    function (Scope $scope) use ($b, $provider): self {
                        return $this->withProvider($b, $provider)->withScope($b, $scope);
                    }
                );
            },
            $binding
        );
    }

    public function inject(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): EnhancementBinder {
        return new EnhancementBinder(new Binding($type, $tag, $context));
    }

    public function enhance(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): EnhancementBinder {
        return new EnhancementBinder(new Binding($type, $tag, $context));
    }

    public function decorate(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): DecoratorBinder {
        return new DecoratorBinder(new Binding($type, $tag, $context));
    }

    public function scope(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ScopeBinder {
        $binding = new Binding($type, $tag, $context);

        return new ScopeBinder(
            function (Scope $scope) use ($binding): self {
                return $this->withScope($binding, $scope);
            }
        );
    }

    // Registry

    public function findProvider(Binding $binding): Provider
    {
        foreach ($binding->hierarchy() as $candidate) {
            if ($this->providers->containsKey($candidate)) {
                return $this->providers->get($candidate);
            }

            if ($this->chains->containsKey($candidate)) {
                return new ContainerCall($this->chains->get($candidate));
            }
        }
    }

    public function hasProvider(Binding $binding): bool
    {
        return $this->providers->containsKey($binding);
    }

    public function findScope(Binding $binding): Scope
    {
        foreach ($binding->hierarchy() as $candidate) {
            $hash = $candidate->hash();

            if ($this->scopes->containsKey($hash)) {
                return $this->scopes->get($hash);
            }
        }

        return Scope::TRANSIENT;
    }

    /** @return iterable<Enhancement> */
    public function findEnhancements(Binding $binding): iterable
    {
        $result = [];

        foreach ($binding->hierarchy() as $candidate) {
            $hash = $candidate->hash();

            if (! $this->enhancements->containsKey($hash)) {
                continue;
            }

            foreach ($this->enhancements->get($hash) as $wrapper) {
                $result[] = $wrapper;
            }
        }

        return $result;
    }
}
