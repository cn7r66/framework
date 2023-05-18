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
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Provider\ContainerCall */
final class ContainerCallTest extends TestCase
{
    /**
     * @covers ::provide()
     * @covers ::getKey()
     */
    public function testProvide(): void
    {
        $container = static::createMock(Container::class);

        $key      = new Key(Stub::class);
        $instance = new StubImpl();

        $container->expects(static::once())
                  ->method('get')
                  ->with($key)
                  ->willReturn($instance);

        $provider = new ContainerCall($key);

        static::assertSame($instance, $provider->provide($container));
        static::assertSame($key, $provider->getKey());
    }
}
