<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\PrivateStub;

/** @coversDefaultClass Vivarium\Container\Reflection\MethodCall */
final class MethodCallTest extends TestCase
{
    /** 
     * @covers ::invoke() 
     * @covers ::isAccessible()
     * @covers ::getReflector() 
     */
    public function testInvoke(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new MethodCall('setInt'))
                            ->bindParameter('n')
                            ->toInstance(42);

        $stub = new ConcreteStub();

        static::assertSame(0, $method->invoke($container, $stub));
        static::assertSame(42, $method->invoke($container, $stub));
    }

    /**
     * @covers ::__construct()
     * @covers ::invoke()
     * @covers ::isAccessible()
     * @covers ::getReflector() 
     * @covers ::makeAccessible()
     */
    public function testPrivateInvoke(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new MethodCall('setInt'))
                            ->bindParameter('n')
                            ->toInstance(42)
                            ->makeAccessible();

        $stub = new PrivateStub();

        static::assertSame(0, $method->invoke($container, $stub));
        static::assertSame(42, $method->invoke($container, $stub));
    }
}
