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
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Exception\BindingNotFound;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\ReflectionContainer;
use Vivarium\Container\Step;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;

/** @coversDefaultClass Vivarium\Container\ReflectionContainer */
final class ReflectionContainerTest extends TestCase
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
        $container = new ReflectionContainer();

        static::assertSame($container->has($id), $result);
    }

    /**
     * @covers ::__construct()
     * @covers ::get()
     */
    public function testGet(): void
    {
        $container = new ReflectionContainer();
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

        $container = new ReflectionContainer();
        $container->get('theId');
    }

    /**
     * @covers ::withStep()
     * @covers ::get()
     * @covers ::has()
     * @covers ::solve()
     */
    public function testWithStep(): void
    {
        $step = $this->getMockBuilder(Step::class)
                     ->getMock();

        $step->expects(static::once())
             ->method('solve')
             ->with($this->equalTo(new SimpleBinding('theId')))
             ->willReturn(new Prototype(ConcreteStub::class));

        $container = (new ReflectionContainer())
                            ->withStep($step);

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
