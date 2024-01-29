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
use Vivarium\Container\Injection\PrivateMethodCall;
use Vivarium\Test\Container\Stub\PrivateStub;

/** @coversDefaultClass \Vivarium\Container\Injection\PrivateMethodCall */
final class PrivateMethodCallTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::invoke()
     */
    public function testInvoke(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new PrivateMethodCall('setInt'))
                            ->bindParameter('n')
                            ->toInstance(42);

        $stub = new PrivateStub();

        static::assertSame(0, $method->invoke($container, $stub));
        static::assertSame(42, $method->invoke($container, $stub));
    }

    /** @covers ::inject() */
    public function testInject(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $method = (new PrivateMethodCall('setInt'))
                            ->bindParameter('n')
                            ->toInstance(42);

        $stub = new PrivateStub();

        $stub1 = $method->inject($container, $stub);

        static::assertSame($stub, $stub1);
        static::assertSame(42, $stub1->getInt());
    }
}
