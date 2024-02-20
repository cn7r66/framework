<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\StaticMethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticInjectorStub;
use Vivarium\Test\Container\Stub\StaticStub;

/** @coversDefaultClass Vivarium\Container\Reflection\StaticMethodCall */
final class StaticMethodCallTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::invoke()
     * @covers ::isAccessible()
     * @covers ::getReflector() 
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

    /** @covers ::getClass() */
    public function testGetClass(): void
    {
        $method = new StaticMethodCall(StaticStub::class, 'get');

        static::assertSame(StaticStub::class, $method->getClass());
    }
}
