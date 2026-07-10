<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Test\Container;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Vivarium\Container\Injector;
use Vivarium\Container\Registry\EagerRegistry;
use Vivarium\Container\Registry\LazyRegistry;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubClass;
use Vivarium\Test\Container\Stub\StubDecorator;
use Vivarium\Test\Container\Stub\StubModule;
use Vivarium\Test\Container\Stub\StubWithNoArgs;

#[CoversNothing]
final class ContainerEndToEndTest extends TestCase
{
    public function testModuleBootstrapResolvesInterfaceToConcreteClass(): void
    {
        $module    = new StubModule();
        $registry  = new LazyRegistry(static fn (): EagerRegistry => $module->configure(new EagerRegistry()));
        $container = new Injector($registry);

        $result = $container->get(Stub::class);

        self::assertInstanceOf(StubDecorator::class, $result);
        self::assertInstanceOf(StubClass::class, $result->inner);
        self::assertNotNull($result->getService());
    }

    public function testDecoratorIsAppliedInFullPipeline(): void
    {
        $module    = new StubModule();
        $registry  = new LazyRegistry(static fn (): EagerRegistry => $module->configure(new EagerRegistry()));
        $container = new Injector($registry);

        $result = $container->get(Stub::class);

        self::assertInstanceOf(StubDecorator::class, $result);
        self::assertInstanceOf(StubClass::class, $result->inner);
    }

    public function testServiceScopeHoldsAcrossFullBootstrap(): void
    {
        $module    = new StubModule();
        $registry  = new LazyRegistry(static fn (): EagerRegistry => $module->configure(new EagerRegistry()));
        $container = new Injector($registry);

        $first  = $container->get(Stub::class);
        $second = $container->get(Stub::class);

        self::assertSame($first, $second);
    }

    public function testLazyRegistryDefersConstructionUntilFirstGet(): void
    {
        $called   = false;
        $registry = new LazyRegistry(static function () use (&$called): EagerRegistry {
            $called = true;

            return new EagerRegistry();
        });

        $container = new Injector($registry);

        self::assertFalse($called);

        $container->get(StubWithNoArgs::class);

        self::assertTrue($called);
    }
}
