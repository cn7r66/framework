<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Exception\EntryNotFound;
use Vivarium\Container\Exception\KeyNotFound;

class SolverContainer implements Container
{
    /** @var Map<Key, Provider> */
    private Map $providers;

    public function __construct(private Solver $solver)
    {
        $this->providers = new HashMap();
    }

    public function get(Key $key): object|array|string|int|float|bool
    {
        if (! $this->has($key)) {
            throw new EntryNotFound('');
        }

        return $this->providers
            ->get($key)
            ->provide($this);
    }

    public function has(Key $key): bool
    {
        try {
            if (! $this->providers->containsKey($key)) {
                $this->providers = $this->providers->put(
                    $key,
                    $this->solver->solve($key),
                );
            }

            return true;
        } catch (KeyNotFound) {
            return false;
        }
    }
}
