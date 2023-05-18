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
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Provider\Cloneable */
final class CloneableTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvide(): void
    {
        $container = static::createMock(Container::class);

        $instance = static::getMockBuilder(Stub::class)
                          ->addMethods(['__clone'])
                          ->getMock();

        $instance->expects(static::exactly(2))
                 ->method('__clone');

        $provider = static::createMock(Provider::class);

        $provider->expects(static::once())
                 ->method('provide')
                 ->with($container)
                 ->willReturn($instance);

        $cloneable = new Cloneable($provider);

        $cloned1 = $cloneable->provide($container);
        $cloned2 = $cloneable->provide($container);

        static::assertNotSame($instance, $cloned1);
        static::assertNotSame($cloned1, $cloned2);
    }

    /** @covers ::getKey() */
    public function testGetKey(): void
    {
        $key = new Key('int');

        $provider = static::createMock(Provider::class);

        $provider->expects(static::once())
                 ->method('getKey')
                 ->willReturn($key);

        $cloneable =  new Cloneable($provider);

        static::assertSame($key, $cloneable->getKey());
    }
}
