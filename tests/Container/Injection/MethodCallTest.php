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
use Vivarium\Container\Injection\MethodCall;
use Vivarium\Container\Provider;
use Vivarium\Test\Container\Stub\StubClass;
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubWithPrivateMethodInjection;

/** @coversDefaultClass \Vivarium\Container\Injection\MethodCall */
final class MethodCallTest extends TestCase
{
    /** @covers ::__construct */
    public function testThrowsForNonExistentMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new MethodCall(StubClass::class, 'nonExistent');
    }

    /** @covers ::__construct */
    public function testThrowsForPrivateMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new MethodCall(StubWithPrivateMethodInjection::class, 'setService');
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceCallsMethodAndReturnsSameInstance(): void
    {
        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn($service);

        $instance  = new StubClass(new StubService());
        $injection = new MethodCall(StubClass::class, 'setService');
        $result    = $injection->enhance($instance, $container);

        static::assertSame($instance, $result);
        static::assertSame($service, $instance->getService());
    }

    /**
     * @covers ::__construct
     * @covers ::enhance
     */
    public function testEnhanceFailsWhenInstanceTypeIsWrong(): void
    {
        static::expectException(AssertionFailed::class);

        $container = $this->createMock(Container::class);
        $injection = new MethodCall(StubClass::class, 'setService');
        $injection->enhance(new StubService(), $container);
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
            (new MethodCall(StubClass::class, 'setService'))->accept($provider),
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
            (new MethodCall(StubClass::class, 'setService'))->accept($provider),
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
            (new MethodCall(StubClass::class, 'setService'))->accept($provider),
        );
    }
}
