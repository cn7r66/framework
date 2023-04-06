<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Key;

/** @template T */
final class Registry
{
    /** @var Map<Key, T> */
    private Map $map;

    public function __construct()
    {
        /** @psalm-var HashMap<Key, T> */
        $this->map = new HashMap();
    }

    /** @param T $entry */
    public function add(Key $key, $entry): self
    {
        $registry = clone $this;
        $registry->map = $registry->map->put($key, $entry);

        return $registry;
    }

    /** @return T */
    public function get(Key $key)
    {

    }

    public function getExactly(Key $key)
    {
        if (! $this->has($key)) {
            throw new \RuntimeException();
        }

        return $this->map->get($key);
    }

    public function has(Key $key): bool
    {
        return $this->map->containsKey($key);
    }

    public function hasExactly(Key $key): bool
    {

    }
}
