<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Injector;
use Vivarium\Container\Registry;
use Vivarium\Container\Registry\EagerRegistry;
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubWithConstructor;
use Vivarium\Test\Container\Stub\StubWithNoArgs;

/** @coversDefaultClass \Vivarium\Container\Injector */
final class InjectorTest extends TestCase
{
    /**
     * @covers ::get
     * @covers ::has
     */
    public function testAutoWiresConcreteClassWithNoArgs(): void
    {
        $container = new Injector(new EagerRegistry());

        static::assertTrue($container->has(StubWithNoArgs::class));
        static::assertInstanceOf(StubWithNoArgs::class, $container->get(StubWithNoArgs::class));
    }

    /**
     * @covers ::get
     * @covers ::has
     */
    public function testAutoWiresConcreteClassWithDependency(): void
    {
        $container = new Injector(new EagerRegistry());

        $result = $container->get(StubWithConstructor::class);

        static::assertInstanceOf(StubWithConstructor::class, $result);
        static::assertInstanceOf(StubService::class, $result->service);
    }

    /**
     * @covers ::get
     */
    public function testExplicitBindingOverridesAutoWiring(): void
    {
        $instance = new StubWithNoArgs();

        $registry  = (new EagerRegistry())
            ->bind(StubWithNoArgs::class)
            ->toInstance($instance);

        $container = new Injector($registry);

        static::assertSame($instance, $container->get(StubWithNoArgs::class));
    }

    /**
     * @covers ::get
     */
    public function testServiceScopeReturnsSameInstance(): void
    {
        $registry = (new EagerRegistry())
            ->bind(StubWithNoArgs::class)->toConstructor()
            ->scope(StubWithNoArgs::class)->service();

        $container = new Injector($registry);

        static::assertSame(
            $container->get(StubWithNoArgs::class),
            $container->get(StubWithNoArgs::class),
        );
    }

    /**
     * @covers ::get
     */
    public function testTransientScopeReturnsDifferentInstances(): void
    {
        $registry = (new EagerRegistry())
            ->bind(StubWithNoArgs::class)->toConstructor()
            ->scope(StubWithNoArgs::class)->transient();

        $container = new Injector($registry);

        static::assertNotSame(
            $container->get(StubWithNoArgs::class),
            $container->get(StubWithNoArgs::class),
        );
    }

    /**
     * @covers ::has
     */
    public function testHasReturnsFalseForUnknownInterface(): void
    {
        $container = new Injector(new EagerRegistry());

        static::assertFalse($container->has(Registry::class));
    }
}
