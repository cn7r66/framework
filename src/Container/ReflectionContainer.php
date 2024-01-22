<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Iterator;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Queue\ArrayQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Comparator\Priority;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Exception\BindingNotFound;

final class ReflectionContainer implements MultiStepContainer
{
    /** @var Queue<ValueAndPriority<Step>> */
    private Queue $steps;

    /** @var Map<Binding, Provider> */
    private Map $solved;

    public function __construct()
    {
        $this->steps = new ArrayQueue(
            new ValueAndPriority(
                new ReflectionSolver(),
                Priority::VERY_VERY_LOW,
            )
        );

        $this->solved = new HashMap();
    }

    public function get(string|Binding $request): mixed
    {
        $request = $this->makeBinding($request);

        if (! $this->has($request)) {
            throw new BindingNotFound($request);
        }

        return $this->solved
            ->get($request)
            ->provide($this);
    }

    public function has(string|Binding $request): bool
    {
        try {
            $request = $this->makeBinding($request);

            if (! $this->solved->containsKey($request)) {
                $this->solved = $this->solved->put(
                    $request,
                    $this->solve($request),
                );
            }

            return true;
        } catch (BindingNotFound) {
            return false;
        }
    }

    public function withStep(Step $step, int $priority = Priority::NORMAL): self
    {
        $container        = clone $this;
        $container->steps = $container->steps->enqueue(
            new ValueAndPriority($step, $priority),
        );

        return $container;
    }

    private function solve(Binding $request): Provider
    {
        return $this->next(
            $request,
            $this->steps->getIterator(),
        )();
    }

    /**
     * @param Iterator<ValueAndPriority<Solver>> $iterator
     *
     * @return callable(): Provider
     */
    private function next(Binding $request, Iterator $iterator): callable
    {
        if ($iterator->valid()) {
            return function () use ($request, $iterator): Provider {
                $solver = $iterator->current()
                                   ->getValue();

                $iterator->next();

                return $solver->solve(
                    $request,
                    $this->next($request, $iterator),
                );
            };
        }

        return static function () use ($request) {
            throw new BindingNotFound($request);
        };
    }

    private function makeBinding(string|Binding $request): Binding
    {
        return $request instanceof Binding ?
            $request : new SimpleBinding($request);
    }
}
