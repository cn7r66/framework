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
use Vivarium\Container\Binding;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Provider\Factory;
use Vivarium\Test\Container\Stub\StubClass;
use Vivarium\Test\Container\Stub\StubFactory;
use Vivarium\Test\Container\Stub\StubFactoryWithPrivateMethod;
use Vivarium\Test\Container\Stub\StubService;

/** @coversDefaultClass \Vivarium\Container\Provider\Factory */
final class FactoryTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::provide
     */
    public function testProvideCallsInstanceMethodAndReturnsResult(): void
    {
        $factory   = new StubFactory();
        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->expects(static::exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($factory, $service);

        $result = (new Factory(new Binding(StubFactory::class), 'create'))->provide($container);

        static::assertInstanceOf(StubClass::class, $result);
        static::assertSame($service, $result->service);
    }

    /** @covers ::getTarget */
    public function testGetTargetReturnsMethodReturnType(): void
    {
        static::assertSame(
            StubClass::class,
            (new Factory(new Binding(StubFactory::class), 'create'))->getTarget(),
        );
    }

    /** @covers ::__construct */
    public function testThrowsForNonExistentMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new Factory(new Binding(StubFactory::class), 'nonExistent');
    }

    /** @covers ::__construct */
    public function testThrowsForPrivateMethod(): void
    {
        static::expectException(AssertionFailed::class);

        new Factory(new Binding(StubFactoryWithPrivateMethod::class), 'create');
    }

    /** @covers ::getCapabilities */
    public function testGetCapabilitiesDoesNotIncludeInjectable(): void
    {
        $capabilities = (new Factory(new Binding(StubFactory::class), 'create'))->getCapabilities();

        static::assertFalse($capabilities->contains(Capability::INJECTABLE));
        static::assertTrue($capabilities->contains(Capability::INTERCEPTABLE));
        static::assertTrue($capabilities->contains(Capability::DECORABLE));
        static::assertCount(2, $capabilities);
    }
}
