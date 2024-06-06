<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Interception\BaseMethodInterception;
use Vivarium\Container\Interception\MutableMethodInterception;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Interceptor;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass \Vivarium\Container\Provider\Interceptor */
final class InterceptorTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::withInterception()
     */
    public function testProvide(): void
    {
        $container = $this->createMock(Container::class);

        $provider = $this->createMock(Provider::class);
        $provider->expects(static::once())
                 ->method('provide')
                 ->with(static::equalTo($container))
                 ->willReturn(new ConcreteStub());

        $interception1 = new MutableMethodInterception(
            (new MethodCall(ConcreteStub::class, 'setInt'))
                ->bindParameter('n')
                ->toInstance(42)
        );

        $interception2 = new MutableMethodInterception(
            (new MethodCall(ConcreteStub::class, 'setInt'))
                ->bindParameter('n')
                ->toInstance(420)
        );

        $interceptor = (new Interceptor($provider))
            ->withInterception($interception1)
            ->withInterception($interception2);

        $stub = $interceptor->provide($container);
        
        static::assertSame(42, $stub->getInt());
    }

    /**
     * @covers ::__construct()
     * @covers ::getInterceptions()
     */
    public function testGetInterceptions(): void
    {
        $container = $this->createMock(Container::class);
        $provider  = $this->createMock(Provider::class);

        $interception1 = new MutableMethodInterception(
            (new MethodCall(ConcreteStub::class, 'setInt'))
                ->bindParameter('n')
                ->toInstance(42)
        );

        $interception2 = new MutableMethodInterception(
            (new MethodCall(ConcreteStub::class, 'setInt'))
                ->bindParameter('n')
                ->toInstance(420)
        );

        $interceptor = (new Interceptor($provider))
            ->withInterception($interception1)
            ->withInterception($interception2);

        $interceptions = $interceptor->getInterceptions();

        static::assertCount(1, $interceptions);
        static::assertSame($interception1, $interceptions->getAtIndex(0));
    }
}
