<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Injection;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Injection\ImmutableMethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\ImmutableStub;

/** @coversDefaultClass Vivarium\Container\Injection\ImmutableMethodCall */
final class ImmutableMethodCallTest extends TestCase
{
    /** @covers ::invoke() */
    public function testInvoke(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new ImmutableMethodCall('withInt'))
                            ->bindParameter('n')
                            ->toInstance(42);

        $stub  = new ImmutableStub();
        $stub1 = $method->invoke($container, $stub);

        static::assertNotSame($stub, $stub1);
        static::assertSame(42, $stub1->getInt());
    }

    /** @covers ::inject() */
    public function testInject(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new ImmutableMethodCall('withInt'))
                            ->bindParameter('n')
                            ->toInstance(42);

        $stub = new ImmutableStub();

        $stub1 = $method->inject($container, $stub);

        static::assertNotSame($stub, $stub1);
        static::assertSame(42, $stub1->getInt());
    }
}
