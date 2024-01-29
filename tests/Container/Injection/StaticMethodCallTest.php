<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Injection;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Injection\StaticMethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticInjectorStub;
use Vivarium\Test\Container\Stub\StaticStub;

/** @coversDefaultClass Vivarium\Container\Injection\StaticMethodCall */
final class StaticMethodCallTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::invoke()
     */
    public function testInvoke(): void
    {
        $method = new StaticMethodCall(
            StaticStub::class,
            'get',
        );

        $method = $method->bindParameter('stub')
                         ->to(ConcreteStub::class);

        $binding = new TypeBinding(ConcreteStub::class);

        $container = $this->createMock(Container::class);

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willReturn(new ConcreteStub());

        $instance = $method->invoke($container);

        static::assertInstanceOf(SimpleStub::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::inject()
     * @covers ::instanceOn()
     */
    public function testInject(): void
    {
        $method = new StaticMethodCall(
            StaticInjectorStub::class,
            'get',
        );

        $method = $method->instanceOn('stub');

        $container = $this->createMock(Container::class);

        $container->expects(static::never())
                  ->method('get');

        $instance = new ConcreteStub();

        static::assertSame(0, $instance->getInt());

        $instance = $method->inject($container, $instance);

        static::assertSame(42, $instance->getInt());
    }

    /** @covers ::getClass() */
    public function testGetClass(): void
    {
        $method = new StaticMethodCall(StaticStub::class, 'get');

        static::assertSame(StaticStub::class, $method->getClass());
    }
}
