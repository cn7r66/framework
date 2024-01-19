<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Provider;
use Vivarium\Container\ReflectionContainer;
use Vivarium\Container\ReflectionSolver;

/** @coversDefaultClass Vivarium\Container\ReflectionSolver */
final class ReflectionSolverTest extends TestCase
{
    /** @covers ::solve() */
    public function testSolve(): void
    {
        $solver = new ReflectionSolver();
        $provider = $solver->solve(
            new ClassBinding(stdClass::class),
            function () {
                throw new RuntimeException();
            }
        );

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(stdClass::class, $provider->provide(new ReflectionContainer()));
    }
}
