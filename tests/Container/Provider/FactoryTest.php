<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider\Factory;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Provider\Factory */
final class FactoryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::getKey()
     */
    public function testProvide(): void
    {
        $instance = new StubImpl();

        $factory = static::getMockBuilder(stdClass::class)
                         ->addMethods(['create'])
                         ->getMock();

        $factory->expects(static::once())
                ->method('create')
                ->willReturn($instance);

        $factoryKey = new Key(stdClass::class);

        $container = static::createMock(Container::class);

        $container->expects(static::once())
                  ->method('get')
                  ->with($factoryKey)
                  ->willReturn($factory);

        $key = new Key(Stub::class);

        $provider = new Factory(
            $key,
            $factoryKey,
            'create',
        );

        static::assertSame($instance, $provider->provide($container));
        static::assertSame($key, $provider->getKey());
    }
}
