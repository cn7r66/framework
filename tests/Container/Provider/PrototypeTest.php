<?php declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Definition\Prototype;
use Vivarium\Test\Container\Stub\Foo;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Definition\Prototype */
final class PrototypeTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::getKey()
     * @covers ::withArgument()
     */
    public function testProvide(): void
    {
        $instance = new StubImpl();

        $container = static::createMock(Container::class);

        $provider = static::createMock(Provider::class);
        $provider->expects(static::once())
            ->method('provide')
            ->with($container)
            ->willReturn($instance);

        $key = new Key(Foo::class);
        $prototype = (new Prototype($key))
            ->withArgument($provider);

        $result = $prototype->provide($container);

        static::assertInstanceOf(Foo::class, $result);
        static::assertSame($instance, $result->getStub());
        static::assertSame($key, $prototype->getKey());
    }
}
