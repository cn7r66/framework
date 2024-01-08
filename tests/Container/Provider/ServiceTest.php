<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Definition\Service */
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
        $instance  = new StubImpl();
        $key       = new Key(Stub::class);

        $provider = static::createMock(Provider::class);
        $provider->expects(static::once())
                 ->method('provide')
                 ->with($container)
                 ->willReturn($instance);

        $provider->expects(static::once())
            ->method('getKey')
            ->willReturn($key);

        $service = new Provider\Service(
            $provider,
        );

        static::assertSame($instance, $service->provide($container));
        static::assertSame($instance, $service->provide($container));
        static::assertSame($provider, $service->getProvider());
        static::assertSame($key, $service->getKey());
    }
}
