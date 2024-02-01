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
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass \Vivarium\Container\GenericBinder */
final class GenericBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::to()
     */
    public function testTo(): void
    {
        $binder = new GenericBinder(function (Provider $provider): void {
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

        $binder = new GenericBinder(function (Provider $provider) use ($instance): void {
            $binding = new TypeBinding(ConcreteStub::class);

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

        $binder = new GenericBinder(static function (Provider $provider1) use ($provider): void {
            static::assertSame($provider, $provider1);
        });

        $binder->toProvider($provider);
    }

    /** @covers ::__construct() */
    public function testConstructorWithoutReturn(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Missing type hint on callback function.');

        new GenericBinder(static function (Provider $provider): void {
        });
    }
}
