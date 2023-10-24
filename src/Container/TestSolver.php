<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\MultiMap\MultiMap;
use Vivarium\Collection\MultiMap\MultiValueMap;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Comparator\IntegerComparator;
use Vivarium\Container\Provider\Prototype;

final class TestSolver implements Solver
{
    /** @var Map<Key, Provider> */
    private Map $providers;

    /** @var MultiMap<Key, Injection> */
    private MultiMap $injections;

    /** @var MultiMap<Key, Interceptor> */
    private MultiMap $interceptors;

    public function __construct()
    {
        $this->providers    = new HashMap();
        $this->injections   = new MultiValueMap(function () {
            return new PriorityQueue(new IntegerComparator());
        });

        $this->interceptors = new MultiValueMap(function () {
            return new PriorityQueue(new IntegerComparator());
        });
    }

    public function solve(Key $key): Provider
    {
        $provider = $this->providers->containsKey($key) ?
            $this->providers->get($key) : new Prototype($key);

        $provider = $this->inject($provider);

        foreach ($this->interceptors as $interceptor) {
            $provider = $interceptor->intercept($provider);
            if ($interceptor->isInjectable()) {
                $provider = $this->inject($provider);
            }
        }

        return $provider;
    }

    public function withProvider(Provider $provider): self
    {
        $solver = clone $this;
        $solver->providers = $solver->providers->put($provider->getKey(), $provider);

        return $solver;
    }

    public function withInjection(Key $key, Injection $injection): self
    {
        $solver = clone $this;
        $solver->injections = $solver->injections->put($key, $injection);

        return $solver;
    }

    public function withInterceptor(Key $key, Interceptor $interceptor): self
    {
        $solver = clone $this;
        $solver->interceptors = $solver->interceptors->put($key, $interceptor);

        return $solver;
    }

    private function inject(Provider $provider): Provider
    {

    }
}
