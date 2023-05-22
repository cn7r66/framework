<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Exception\KeyNotFound;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver\MultiStepSolver;
use Vivarium\Container\Solver\SolverStep;

/** @coversDefaultClass \Vivarium\Container\Solver\MultiStepSolver */
final class MultiStepSolverTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::solve()
     * @covers ::next()
     */
    public function testSolve(): void
    {
        $provider = static::createMock(Provider::class);

        $step1 = static::createMock(SolverStep::class);
        $step2 = static::createMock(SolverStep::class);

        $step1->expects(static::once())
              ->method('solve')
              ->willReturnCallback(static function (Key $key, callable $next): mixed {
                  return $next($key);
              });

        $step2->expects(static::once())
              ->method('solve')
              ->willReturn($provider);

        $solver = new MultiStepSolver(
            $step1,
            $step2,
        );

        static::assertSame($provider, $solver->solve(new Key('int')));
    }

    /**
     * @covers ::__construct()
     * @covers ::solve()
     * @covers ::next()
     */
    public function testSolveEmptySolver(): void
    {
        static::expectException(KeyNotFound::class);

        (new MultiStepSolver())
            ->solve(new Key('int'));
    }

    /**
     * @covers ::__construct()
     * @covers ::solve()
     * @covers ::next()
     */
    public function testSolveException(): void
    {
        static::expectException(KeyNotFound::class);

        $step1 = static::createMock(SolverStep::class);
        $step2 = static::createMock(SolverStep::class);

        $step1->expects(static::once())
            ->method('solve')
            ->willReturnCallback(static function (Key $key, callable $next): mixed {
                return $next($key);
            });

        $step2->expects(static::once())
            ->method('solve')
            ->willReturnCallback(static function (Key $key, callable $next): mixed {
                return $next($key);
            });

        $solver = new MultiStepSolver(
            $step1,
            $step2,
        );

        $solver->solve(new Key('int'));
    }
}
