<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\ConfigurableSolver;
use Vivarium\Container\LazySolver;
use Vivarium\Container\Module;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Instance;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\LazySolver */
final class LazySolverTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::solve()
     */
    public function testSolve(): void
    {
        $configurable = $this->createMock(ConfigurableSolver::class);

        $binding = new ClassBinding(Stub::class);

        $configurable->expects(static::once())
                     ->method('solve')
                     ->with(static::equalTo($binding))
                     ->willReturn(new Instance(new ConcreteStub()));

        $solver = new LazySolver(function () use ($configurable) {
            $module = $this->createMock(Module::class);
            $module->expects(static::once())
                   ->method('install')
                   ->with(static::equalTo($configurable))
                   ->willReturnArgument(0);

            return [$module];
        }, $configurable);

        $instance = $solver->solve($binding, static function (): void {
        });

        static::assertInstanceOf(Provider::class, $instance);
    }
}
