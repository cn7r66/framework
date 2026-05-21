<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Provider\StaticFactory;
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubStaticFactory;
use Vivarium\Test\Container\Stub\StubStaticFactoryWithPrivateMethod;
use Vivarium\Test\Container\Stub\StubWithConstructor;

/** @coversDefaultClass \Vivarium\Container\Provider\StaticFactory */
final class StaticFactoryTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::provide
     */
    public function testProvideCallsStaticMethodAndReturnsResult(): void
    {
        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn($service);

        $result = (new StaticFactory(StubStaticFactory::class, 'create'))->provide($container);

        static::assertInstanceOf(StubWithConstructor::class, $result);
        static::assertSame($service, $result->service);
    }

    /** @covers ::getTarget */
    public function testGetTargetReturnsMethodReturnType(): void
    {
        static::assertSame(
            StubWithConstructor::class,
            (new StaticFactory(StubStaticFactory::class, 'create'))->getTarget(),
        );
    }

    /** @covers ::__construct */
    public function testThrowsForNonExistentMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new StaticFactory(StubStaticFactory::class, 'nonExistent');
    }

    /** @covers ::__construct */
    public function testThrowsForPrivateMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new StaticFactory(StubStaticFactoryWithPrivateMethod::class, 'create');
    }

    /** @covers ::getCapabilities */
    public function testGetCapabilitiesContainsInterceptableAndDecorable(): void
    {
        $capabilities = (new StaticFactory(StubStaticFactory::class, 'create'))->getCapabilities();

        static::assertFalse($capabilities->contains(Capability::INJECTABLE));
        static::assertTrue($capabilities->contains(Capability::INTERCEPTABLE));
        static::assertTrue($capabilities->contains(Capability::DECORABLE));
        static::assertCount(2, $capabilities);
    }
}
