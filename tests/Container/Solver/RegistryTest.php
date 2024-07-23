<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding;
use Vivarium\Container\Definition;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\InstanceMethod;
use Vivarium\Container\Solver\Registry;
use Vivarium\Test\Container\Stub\BaseStub;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticStub;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Solver\Registry */
final class RegistryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::bind()
     * @covers ::hasProvider()
     */
    public function testBind(): void
    {
        $registry = (new Registry())
            ->bind(Stub::class)
            ->to(ConcreteStub::class);

        static::assertTrue($registry->hasProvider(Stub::class));
    }

    /**
     * @covers ::__construct()
     * @covers ::define()
     * @covers ::hasProvider()
     */
    public function testDefine(): void
    {
        $registry = (new Registry())
            ->define(ConcreteStub::class)
            ->as(function (Definition $definition) {
                return $definition->bindMethod('setInt', function (InstanceMethod $method) {
                    return $method->bindParameter('n')
                                  ->toInstance(42);
                });
            });

        static::assertTrue($registry->hasProvider(ConcreteStub::class));
    }

    /**
     * @covers ::__construct()
     * @covers ::extend()
     * @covers ::hasProvider()
     */
    public function testExtend(): void
    {
        $registry = (new Registry())
            ->define(ConcreteStub::class)
            ->as(function (Definition $definition) {
                return $definition->bindMethod('setInt', function (InstanceMethod $method) {
                    return $method->bindParameter('n')
                                  ->toInstance(42);
                });
            });

        $registry->extend(ConcreteStub::class)
                 ->as(function (Provider $provider): Provider {
                    static::assertInstanceOf(Definition::class, $provider);

                    return $provider;
                 });

        static::assertTrue($registry->hasProvider(ConcreteStub::class));
    }

    /**
     * @covers ::__construct()
     * @covers ::scope()
     * @covers ::hasScope()
     */
    public function testScope(): void
    {
        $registry = (new Registry())
            ->scope(ConcreteStub::class)
            ->service();

        static::assertTrue($registry->hasScope(ConcreteStub::class));
    }

    /**
     * @covers ::__construct()
     * @covers ::intercept()
     */
    public function testIntercept(): void
    {
        $registry = (new Registry())
            ->intercept(Stub::class)
            ->withMethod('setInt', function (InstanceMethod $method) {
                return $method->bindParameter('n')
                              ->toInstance(42);
            });

        static::assertTrue($registry->hasInterceptions(Stub::class));
    }

    /**
     * @covers ::hasInterceptions()
     */
    public function testHasInterceptions(): void
    {
        $registry = (new Registry())
            ->intercept(Stub::class)
            ->withMethod('setInt', function (InstanceMethod $method) {
                return $method->bindParameter('n')
                              ->toInstance(42);
            });

        static::assertTrue($registry->hasInterceptions(Stub::class));
        static::assertTrue($registry->hasInterceptions(BaseStub::class));
        static::assertTrue($registry->hasInterceptions(ConcreteStub::class));
        static::assertFalse($registry->hasInterceptions(SimpleStub::class));
    }

    /**
     * @covers ::__construct()
     * @covers ::hasProvider()
     */
    public function testHasProvider(): void
    {
        $registry = (new Registry())
            ->bind(Stub::class)
            ->to(ConcreteStub::class);

        static::assertTrue($registry->hasProvider(Stub::class));
        static::assertTrue($registry->hasProvider(Stub::class, 'myStub'));
        static::assertTrue($registry->hasProvider(Stub::class, Binding::DEFAULT, 'Vivarium\Test\Container'));
        static::assertFalse($registry->hasProvider(StaticStub::class));
        static::assertFalse($registry->hasProvider(BaseStub::class));
    }
}
