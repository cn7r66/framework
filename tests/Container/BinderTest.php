<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Binder;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Provider\StaticFactory;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\StaticStub;
use Vivarium\Test\Container\Stub\StubFactory;

/** @coversDefaultClass \Vivarium\Container\Binder */
final class BinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::to()
     */
    public function testTo(): void
    {
        $binder = new Binder(function (Provider $provider): void {
            $binding = new TypeBinding(ConcreteStub::class);

            $container = $this->createMock(Container::class);

            $container->expects(static::once())
                      ->method('get')
                      ->with(static::equalTo($binding));

            static::assertInstanceOf(ContainerCall::class, $provider);

            $provider->provide($container);
        });

        $binder->to(ConcreteStub::class);
    }

    /**
     * @covers ::__construct()
     * @covers ::toInstance()
     */
    public function testToInstance(): void
    {
        $instance = new ConcreteStub();

        $binder = new Binder(function (Provider $provider) use ($instance): void {
            $container = $this->createMock(Container::class);

            $container->expects(static::never())
                      ->method('get');

            static::assertSame($instance, $provider->provide($container));
        });

        $binder->toInstance($instance);
    }

    /** @covers ::toProvider() */
    public function testToProvider(): void
    {
        $provider = $this->createMock(Provider::class);

        $binder = new Binder(static function (Provider $provider1) use ($provider): void {
            static::assertSame($provider, $provider1);
        });

        $binder->toProvider($provider);
    }

    /** @covers ::toFactory */
    public function testToFactory(): void
    {
        $binder = new Binder(static function (Provider $provider): void {
            static::assertInstanceOf(Factory::class, $provider);
        });

        $binder->toFactory(StubFactory::class)
               ->method('create', static function (CreationalMethod $method) {
                    return $method->bindParameter('stub')
                                  ->to(ConcreteStub::class);
               });
    }

    /** @covers ::toStaticFactory */
    public function testToStaticFactory(): void
    {
        $binder = new Binder(static function (Provider $provider): void {
            static::assertInstanceOf(StaticFactory::class, $provider);
        });

        $binder->toStaticFactory(StaticStub::class)
                ->method('get', static function (CreationalMethod $method) {
                    return $method->bindParameter('stub')
                                  ->to(ConcreteStub::class);
                });
    }

    /** @covers ::__construct() */
    public function testConstructorWithoutReturn(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Missing type hint on callback function.');

        // phpcs:ignore
        new Binder(static function (Provider $provider) {
        });
    }
}
