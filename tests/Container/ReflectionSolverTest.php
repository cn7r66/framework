<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use stdClass;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Provider;
use Vivarium\Container\Container;
use Vivarium\Container\ReflectionSolver;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Test\Container\Stub\NotInstantiableStub;
use Vivarium\Container\Provider\Instance;

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

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(stdClass::class, $provider->provide($container));
    }

    /**
     * @covers ::solve()
     */
    public function testSolveNotClass(): void
    {
        $solver   = new ReflectionSolver();
        $provider = $solver->solve(
            new SimpleBinding('theId'),
            function () {
                return new Prototype(ConcreteStub::class);
            }
        );

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(ConcreteStub::class, $provider->provide($container));
    }

    /** @covers ::solve() */
    public function testSolveNotInstantiable(): void
    {
        $solver   = new ReflectionSolver();
        $provider = $solver->solve(
            new ClassBinding(NotInstantiableStub::class),
            function () {
                $reflector = new ReflectionClass(NotInstantiableStub::class);

                return new Instance($reflector->newInstanceWithoutConstructor());
            }
        );

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(NotInstantiableStub::class, $provider->provide($container));
    }
}
