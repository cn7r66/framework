<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Solver;

use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Container\Provider\Service;

final class ScopeStep implements SolverStep
{
    private const SERVICE = 1;

    private const CLONEABLE = 2;

    /** @var Registry<ScopeStep::SERVICE|ScopeStep::CLONEABLE> */
    private Registry $registry;

    public function __construct()
    {
        /**
         * @psalm-var Registry<ScopeStep::SERVICE|ScopeStep::CLONEABLE>
         * @phpstan-ignore-next-line
         */
        $this->registry = new Registry();
    }

    public function solve(Key $key, callable $next): Provider
    {
        if (! $this->registry->hasExactly($key)) {
            return $next();
        }

        return match ($this->registry->getExactly($key)) {
            self::SERVICE   => new Service($next()),
            self::CLONEABLE => new Cloneable($next())
        };
    }

    public function addService(Key $key): self
    {
        $step           = clone $this;
        $step->registry = $step->registry
            ->add($key, self::SERVICE);

        return $step;
    }

    public function addCloneable(Key $key): self
    {
        $step           = clone $this;
        $step->registry = $step->registry
            ->add($key, self::CLONEABLE);

        return $step;
    }
}
