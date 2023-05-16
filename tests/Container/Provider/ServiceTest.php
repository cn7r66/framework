<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Test\Container\Stub\StubImpl;
use Vivarium\Test\Container\Stub\StubInterface;

/** @coversDefaultClass \Vivarium\Container\Provider\Service */
final class ServiceTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::getProvider()
     * @covers ::getKey()
     */
    public function testProvide(): void
    {
        $container = static::createMock(Container::class);

        $instance = new StubImpl();

        $provider = static::createMock(Provider::class);
        $provider->expects(static::once())
                 ->method('provide')
                 ->with($container)
                 ->willReturn($instance);

        $key = new Key(StubInterface::class);

        $service = new Provider\Service(
            $key,
            $provider
        );

        static::assertSame($instance, $service->provide($container));
        static::assertSame($instance, $service->provide($container));
        static::assertSame($provider, $service->getProvider());
        static::assertSame($key, $service->getKey());
    }
}