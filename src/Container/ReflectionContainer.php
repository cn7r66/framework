<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Iterator;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Collection\Queue\ArrayQueue;
use Vivarium\Collection\Queue\Queue;
use Vivarium\Comparator\Priority;
use Vivarium\Comparator\ValueAndPriority;
use Vivarium\Container\Provider\Prototype;

use function class_exists;

final class ReflectionContainer implements Container
{
    /** @var Queue<ValueAndPriority<Solver>> */
    private Queue $solvers;

    /** @var Map<Key, Provider> */
    private Map $solved;

    public function __construct()
    {
        $this->solvers = new ArrayQueue();
        $this->solved  = new HashMap();
    }

    public function get(Key $request): mixed
    {
        if (! $this->has($request)) {
            throw new RuntimeException();
        }

        return $this->solved
            ->get($request)
            ->provide($this);
    }

    public function has(Key $request): bool
    {
        try {
            if (! $this->solved->containsKey($request)) {
                $this->solved = $this->solved->put(
                    $request,
                    $this->solve($request),
                );
            }

            return true;
        } catch (RuntimeException) {
            return false;
        }
    }

    public function withSolver(Solver $solver, int $priority = Priority::NORMAL): self
    {
        $container          = clone $this;
        $container->solvers = $container->solvers->enqueue(
            new ValueAndPriority($solver, $priority),
        );

        return $container;
    }

    private function solve(Key $request): Provider
    {
        return $this->next(
            $request,
            $this->solvers->getIterator(),
        )();
    }

    /**
     * @param Iterator<ValueAndPriority<Solver>> $iterator
     *
     * @return callable(): Provider
     */
    private function next(Key $request, Iterator $iterator): callable
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

        return static function () use ($request): Provider {
            if (! class_exists($request->getType())) {
                throw new RuntimeException();
            }

            try {
                $reflector = new ReflectionClass($request->getType());
                if (! $reflector->isInstantiable()) {
                    throw new RuntimeException();
                }

                return new Prototype($request);
            } catch (ReflectionException) {
                  throw new RuntimeException();
            }
        };
    }
}
