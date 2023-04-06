<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Iterator;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Exception\KeyNotFound;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver;

final class MultiStepSolver implements Solver
{
    /** @var Sequence<SolverStep> */
    private Sequence $steps;

    public function __construct(SolverStep ...$steps)
    {
        $this->steps = new ArraySequence(...$steps);
    }

    public function solve(Key $key): Provider
    {
        return $this->next(
            $key,
            $this->steps->getIterator()
        )();
    }

    /**
     * @param Key                  $key
     * @param Iterator<SolverStep> $iterator
     *
     * @return callable(): Provider
     */
    private function next(Key $key, Iterator $iterator): callable
    {
        return function () use ($key, $iterator): Provider {
            if (! $iterator->valid()) {
                throw new KeyNotFound();
            }

            $step = $iterator->current();
            $iterator->next();

            return $step->solve(
                $key,
                $this->next($key, $iterator)
            );
        };
    }
}
