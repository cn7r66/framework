<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver;

final class CachedStep implements SolverStep
{
    public function solve(Key $key, callable $next): Provider
    {
        if ($this->has($key)) {
            return $this->hit($key);
        }

        return $this->cache($next());
    }

    private function has(Key $key): bool
    {
        return false;
    }

    private function cache(Provider $provider): Provider
    {
        return $provider;
    }

    private function hit(Key $key): Provider
    {
        throw new \RuntimeException('Not implemented yet.');
    }
}
