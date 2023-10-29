<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Collection\Collection;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Set\SortedSet;
use Vivarium\Container\Injection\InjectionComparator;
use Vivarium\Container\Provider\Injector;
use Vivarium\Container\Provider\Prototype;

final class ReflectionContainer implements Container
{
    /** @var Map<Key, Provider> */
    private Map $solved;

    /** @var Map<Key, Provider> */
    private Map $providers;

    /** @var MultiMap<Key, Injection> */
    private MultiMap $injections;

    /** @var MultiMap<Key, Injection> */
    private MultiMap $globalInjections;

    public function __construct()
    {
        $this->solved     = new HashMap();
        $this->providers  = new HashMap();
        $this->injections = new MultiValueMap(static function () {
            return new PriorityQueue(new InjectionComparator());
        });
        $this->globalInjections = new MultiValueMap(static function () {
            return new SortedSet(new InjectionComparator());
        });
    }

    public function get(Key $key): mixed
    {
        if ($this->solved->containsKey($key)) {
            return $this->solved
                ->get($key)
                ->provide($this);
        }

        $provider = $this->inject(
            $this->getProvider($key),
            $key,
        );
    }

    public function has(Key $key): bool
    {
        return $this->providers->containsKey($key);
    }

    private function getProvider(Key $key): Provider
    {
        while (! $this->providers->containsKey($key)) {
            if (! $key->couldBeWidened()) {
                return new Prototype(
                    new Key($key->getType()),
                );
            }

            $key = $key->widen();
        }

        return $this->providers->get($key);
    }

    private function inject(Provider $provider, Key|null $source = null): Provider
    {
        $key = $source ?? $provider->getKey();

        $injector = new Injector($provider);

        $injections = $this->injections->get($key);
    }
}
