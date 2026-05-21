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
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubWithMethodInjection;
use Vivarium\Test\Container\Stub\StubWithPrivateMethodInjection;

/** @coversDefaultClass \Vivarium\Container\Injection\ImmutableMethodCall */
final class ImmutableMethodCallTest extends TestCase
{
    /** @covers ::__construct */
    public function testThrowsForNonExistentMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new ImmutableMethodCall(StubWithMethodInjection::class, 'nonExistent');
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

        $instance  = new StubWithMethodInjection();
        $injection = new ImmutableMethodCall(StubWithMethodInjection::class, 'withService');
        $result    = $injection->enhance($instance, $container);

        static::assertNotSame($instance, $result);
        static::assertInstanceOf(StubWithMethodInjection::class, $result);
        static::assertSame($service, $result->getService());
        static::assertNull($instance->getService());
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceFailsWhenInstanceTypeIsWrong(): void
    {
        static::expectException(AssertionFailed::class);

        $container = $this->createMock(Container::class);
        $injection = new ImmutableMethodCall(StubWithMethodInjection::class, 'withService');
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
        $provider->method('getTarget')->willReturn(StubWithMethodInjection::class);

        static::assertTrue(
            (new ImmutableMethodCall(StubWithMethodInjection::class, 'withService'))->accept($provider),
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
            (new ImmutableMethodCall(StubWithMethodInjection::class, 'withService'))->accept($provider),
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
            (new ImmutableMethodCall(StubWithMethodInjection::class, 'withService'))->accept($provider),
        );
    }
}

class StubWithWrongReturn
{
    public function withSomething(StubService $service): StubService
    {
        return $service;
    }
}
