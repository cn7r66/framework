<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Injection;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Container;
use Vivarium\Container\Injection\ImmutableMethodCall;
use Vivarium\Container\Provider;
use Vivarium\Test\Container\Stub\StubClass;
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubWithPrivateMethodInjection;
use Vivarium\Test\Container\Stub\StubWithWrongReturn;

/** @coversDefaultClass \Vivarium\Container\Injection\ImmutableMethodCall */
final class ImmutableMethodCallTest extends TestCase
{
    /** @covers ::__construct */
    public function testThrowsForNonExistentMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new ImmutableMethodCall(StubClass::class, 'nonExistent');
    }

    /** @covers ::__construct */
    public function testThrowsForPrivateMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new ImmutableMethodCall(StubWithPrivateMethodInjection::class, 'setService');
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceReturnsNewInstanceWithDependencyInjected(): void
    {
        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn($service);

        $initialService = new StubService();
        $instance       = new StubClass($initialService);
        $injection      = new ImmutableMethodCall(StubClass::class, 'withService');
        $result         = $injection->enhance($instance, $container);

        static::assertNotSame($instance, $result);
        static::assertInstanceOf(StubClass::class, $result);
        static::assertSame($service, $result->getService());
        static::assertSame($initialService, $instance->getService());
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceFailsWhenInstanceTypeIsWrong(): void
    {
        static::expectException(AssertionFailed::class);

        $container = $this->createMock(Container::class);
        $injection = new ImmutableMethodCall(StubClass::class, 'withService');
        $injection->enhance(new StubService(), $container);
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceFailsWhenMethodReturnsWrongType(): void
    {
        static::expectException(AssertionFailed::class);

        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn($service);

        $injection = new ImmutableMethodCall(StubWithWrongReturn::class, 'withSomething');
        $injection->enhance(new StubWithWrongReturn(), $container);
    }

    /**
     * @covers ::__construct
     * @covers ::accept
     */
    public function testAcceptReturnsTrueWhenTargetHasMethod(): void
    {
        $provider = $this->createMock(Provider::class);
        $provider->method('getTarget')->willReturn(StubClass::class);

        static::assertTrue(
            (new ImmutableMethodCall(StubClass::class, 'withService'))->accept($provider),
        );
    }

    /**
     * @covers ::__construct
     * @covers ::accept
     */
    public function testAcceptReturnsFalseWhenTargetLacksMethod(): void
    {
        $provider = $this->createMock(Provider::class);
        $provider->method('getTarget')->willReturn(StubService::class);

        static::assertFalse(
            (new ImmutableMethodCall(StubClass::class, 'withService'))->accept($provider),
        );
    }

    /**
     * @covers ::__construct
     * @covers ::accept
     */
    public function testAcceptReturnsFalseWhenTargetIsNotAClass(): void
    {
        $provider = $this->createMock(Provider::class);
        $provider->method('getTarget')->willReturn('string');

        static::assertFalse(
            (new ImmutableMethodCall(StubClass::class, 'withService'))->accept($provider),
        );
    }
}
