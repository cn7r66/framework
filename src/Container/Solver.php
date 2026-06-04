<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Closure;
use Vivarium\Check\CheckIfType;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Pair\Pair;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Comparator\SortableComparator;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding\DecoratorBinder;
use Vivarium\Container\Binding\EnhancementBinder;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Definition\Cloneable;
use Vivarium\Container\Definition\Service;
use Vivarium\Container\Definition\Transient;
use Vivarium\Container\Provider\Constructor;

/** @template T */
final class Solver implements ResolutionNode, Bindable
{
    /** @var Map<string, Pair<Provider, Scope>> */
    private Map $bindings;

    /** @var PriorityQueue<ValueAndPriority<Enhancement>> */
    private PriorityQueue $enhancements;

    public function __construct()
    {
        $this->bindings     = new HashMap();
        $this->enhancements = new PriorityQueue(
            new SortableComparator(),
        );
    }

    /** @param Closure(): Definition $next */
    public function resolve(Binding $binding, Closure $next): Definition
    {
        $pair = $this->findBinding($binding);

        if ($pair === null) {
            return $next();
        }

        return $this->buildDefinition(
            $pair->getKey(),
            $pair->getValue(),
        );
    }

    /** @return ProviderBinder<T> */
    public function bind(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ProviderBinder {
        $binding = new Binding($type, $tag, $context);

        return new ProviderBinder(
            function (Binding $source, Provider $provider) use ($binding): ScopeBinder {
                return new ScopeBinder(
                    function (Scope $scope) use ($binding, $provider): self {
                        $this->bindings = $this->bindings->put(
                            $binding->hash(),
                            new Pair($provider, $scope),
                        );

                        return $this;
                    },
                );
            },
            $binding,
        );
    }

    /** @return EnhancementBinder<T> */
    public function inject(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): EnhancementBinder {
        return new EnhancementBinder(new Binding($type, $tag, $context));
    }

    /** @return EnhancementBinder<T> */
    public function enhance(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): EnhancementBinder {
        return new EnhancementBinder(new Binding($type, $tag, $context));
    }

    /** @return DecoratorBinder<T> */
    public function decorate(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): DecoratorBinder {
        $binding = new Binding($type, $tag, $context);

        return new DecoratorBinder($binding);
    }

    /** @return ScopeBinder<T> */
    public function scope(
        string $type,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): ScopeBinder {
        $binding = new Binding($type, $tag, $context);

        return new ScopeBinder(
            function (Scope $scope) use ($binding): self {
                if ($this->bindings->containsKey($binding->hash())) {
                    $pair           = $this->bindings->get($binding->hash());
                    $this->bindings = $this->bindings->put(
                        $binding->hash(),
                        new Pair($pair->getKey(), $scope),
                    );
                }

                return $this;
            },
        );
    }

    /** @return Pair<Provider, Scope>|null */
    private function findBinding(Binding $binding): Pair|null
    {
        foreach ($binding->hierarchy() as $candidate) {
            if ($this->bindings->containsKey($candidate->hash())) {
                return $this->bindings->get($candidate->hash());
            }
        }

        if (CheckIfType::IsClass($binding->getType())) {
            return new Pair(new Constructor($binding->getType()), Scope::TRANSIENT);
        }

        return null;
    }

    private function buildDefinition(Provider $provider, Scope $scope): Definition
    {
        $definition = match ($scope) {
            Scope::SERVICE   => new Service($provider),
            Scope::CLONEABLE => new Cloneable($provider),
            Scope::TRANSIENT => new Transient($provider),
        };

        foreach ($this->enhancements as $wrapper) {
            $enhancement = $wrapper->getValue();
            if (! $enhancement->accept($provider)) {
                continue;
            }

            $definition = $definition->withEnhancement(
                $enhancement,
                $wrapper->getPriority(),
            );
        }

        return $definition;
    }
}
