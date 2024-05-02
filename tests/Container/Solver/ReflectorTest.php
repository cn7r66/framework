<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use stdClass;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Solver\Reflector;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\NotInstantiableStub;

/** @coversDefaultClass Vivarium\Container\Solver\Reflector */
final class ReflectorTest extends TestCase
{
    /** @covers ::solve() */
    public function testSolve(): void
    {
        $solver   = new Reflector();
        $provider = $solver->solve(
            new ClassBinding(stdClass::class),
            static function (): void {
                throw new RuntimeException();
            },
        );

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(stdClass::class, $provider->provide($container));
    }

    /** @covers ::solve() */
    public function testSolveNotClass(): void
    {
        $solver   = new Reflector();
        $provider = $solver->solve(
            new SimpleBinding('theId'),
            static function () {
                return new Prototype(ConcreteStub::class);
            },
        );

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(ConcreteStub::class, $provider->provide($container));
    }

    /** @covers ::solve() */
    public function testSolveNotInstantiable(): void
    {
        $solver   = new Reflector();
        $provider = $solver->solve(
            new ClassBinding(NotInstantiableStub::class),
            static function () {
                $reflector = new ReflectionClass(NotInstantiableStub::class);

                return new Instance($reflector->newInstanceWithoutConstructor());
            },
        );

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        static::assertInstanceOf(Provider::class, $provider);
        static::assertInstanceOf(NotInstantiableStub::class, $provider->provide($container));
    }
}
