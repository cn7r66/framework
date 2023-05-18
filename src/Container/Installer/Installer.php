<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Assertion\Boolean\IsTrue;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Queue\PriorityQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Container\Exception\StepNotFound;
use Vivarium\Container\Solver\SolverStep;

use function array_map;

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
        /**
         * @psalm-var Map<class-string, SolverStep>
         * @phpstan-ignore-next-line
         */
        $this->steps = new HashMap();

        /**
         * @psalm-var Map<class-string, int>
         * @phpstan-ignore-next-line
         */
        $this->priority = new HashMap();

        /**
         * @psalm-var Map<class-string, callable(): SolverStep>
         * @phpstan-ignore-next-line
         */
        $this->factories = new HashMap();
    }

    public function withStep(SolverStep $step, int|null $priority = null): self
    {
        if ($priority === null) {
            (new IsTrue())
                ->assert(
                    $this->priority->containsKey($step::class),
                    'Must set priority for newly added steps.',
                );
        }

        $installer        = clone $this;
        $installer->steps = $installer->steps
            ->put($step::class, $step);

        if ($priority !== null) {
            $installer->priority = $installer->priority
                ->put($step::class, $priority);
        }

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
     * @template T of SolverStep
     */
    public function getStep(string $class)
    {
        (new IsAssignableTo(SolverStep::class))
            ->assert(
                $class,
                'Class must be of type %2$s.',
            );

        if ($this->steps->containsKey($class)) {
            /** @psalm-var T */
            return $this->steps->get($class);
        }

        if ($this->factories->containsKey($class)) {
            /** @psalm-var T */
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

        return array_map(static function (StepAndPriority $stepAndPriority) {
            return $stepAndPriority->getStep();
        }, $queue->toArray());
    }
}
