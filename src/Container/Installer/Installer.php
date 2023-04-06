<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Assertion\Boolean\IsTrue;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Container\Exception\StepNotFound;
use Vivarium\Container\Solver\SolverStep;

final class Installer
{
    /** @var Map<class-string, SolverStep> */
    private Map $steps;

    /** @var Map<class-string, int> */
    private Map $priority;

    /** @var Map<class-string, callable(): SolverStep> */
    private Map $factories;

    public function __construct()
    {
        $this->steps     = new HashMap();
        $this->priority  = new HashMap();
        $this->factories = new HashMap();
    }

    public function withStep(SolverStep $step): self
    {
        (new IsTrue())
            ->assert(
                $this->steps->containsKey($step::class),
                'Step %s is not registered in this installer.'
            );

        $installer        = clone $this;
        $installer->steps = $installer->steps
            ->put($step::class, $step);

        return $installer;
    }

    /**
     * @param class-string<T> $class
     * @param callable(): T   $factory
     *
     * @template T as SolverStep
     */
    public function withStepFactory(string $class, callable $factory, int $priority): self
    {
        (new IsAssignableTo(SolverStep::class))
            ->assert($class);

        $installer            = clone $this;
        $installer->factories = $installer->factories
            ->put($class, $factory);

        $installer->priority = $installer->priority
            ->put($class, $priority);

        return $installer;
    }

    /**
     * @param class-string<T> $class
     *
     * @return T
     *
     * @template T as SolverStep
     */
    public function getStep(string $class): SolverStep
    {
        (new IsAssignableTo(SolverStep::class))
            ->assert($class);

        if ($this->steps->containsKey($class)) {
            return $this->steps->get($class);
        }

        if ($this->factories->containsKey($class)) {
            return $this->factories->get($class)();
        }

        throw new StepNotFound();
    }

    /** @return array<SolverStep> */
    public function getSteps(): array
    {
        /** @var Queue<StepAndPriority> $queue */
        $queue = new PriorityQueue(
            new StepAndPriorityComparator(),
        );

        foreach ($this->steps as $step) {
            $queue = $queue->enqueue(
                new StepAndPriority(
                    $step,
                    $this->priority->get($step::class),
                ),
            );
        }

        return $queue->toArray();
    }
}
