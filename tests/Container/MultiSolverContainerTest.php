<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Exception\BindingNotFound;
use Vivarium\Container\MultiSolverContainer;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Solver;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;

use function class_exists;

/** @coversDefaultClass Vivarium\Container\MultiSolverContainer */
final class MultiSolverContainerTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::has()
     * @covers ::solve()
     * @covers ::next()
     * @covers ::makeBinding()
     * @dataProvider getContainerIds
     */
    public function testHasWithString(string $id, bool $result): void
    {
        $solver = $this->createMock(Solver::class);
        $solver->method('solve')
               ->willReturnCallback(static function (Binding $arg, callable $next) {
                if (! class_exists($arg->getId())) {
                    return $next();
                }

                return new Prototype($arg->getId());
               });

        $container = (new MultiSolverContainer())->withSolver($solver);

        static::assertSame($container->has($id), $result);
    }

    /**
     * @covers ::__construct()
     * @covers ::get()
     */
    public function testGet(): void
    {
        $solver = $this->createMock(Solver::class);
        $solver->method('solve')
               ->willReturnCallback(static function (Binding $arg) {
                return new Instance(new ($arg->getId()));
               });

        $container = (new MultiSolverContainer())->withSolver($solver);
        $instance  = $container->get(stdClass::class);

        static::assertInstanceOf(stdClass::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::get()
     */
    public function testGetException(): void
    {
        static::expectException(BindingNotFound::class);
        static::expectExceptionMessage('Binding with id theId, context $GLOBAL and tag $DEFAULT not found.');

        $container = new MultiSolverContainer();
        $container->get('theId');
    }

    /**
     * @covers ::withSolver()
     * @covers ::get()
     * @covers ::has()
     * @covers ::solve()
     */
    public function testWithSolver(): void
    {
        $solver = $this->getMockBuilder(Solver::class)
                     ->getMock();

        $solver->expects(static::once())
             ->method('solve')
             ->with($this->equalTo(new SimpleBinding('theId')))
             ->willReturn(new Prototype(ConcreteStub::class));

        $container = (new MultiSolverContainer())
                            ->withSolver($solver);

        static::assertTrue($container->has('theId'));
        static::assertInstanceOf(ConcreteStub::class, $container->get('theId'));
    }

    /** @return array<array-key, array<string, bool>> */
    public static function getContainerIds(): array
    {
        return [
            'Non existent ID' => [
                'theId',
                false,
            ],
            'Class without contructor' => [
                stdClass::class,
                true,
            ],
            'Class with empty constructor' => [
                ConcreteStub::class,
                true,
            ],
            'Class with constructor' => [
                SimpleStub::class,
                true,
            ],
        ];
    }
}
