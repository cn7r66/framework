<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Container;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticStub;

/** @coversDefaultClass \Vivarium\Container\Provider\Prototype */
final class PrototypeTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvideWithConstructor(): void
    {
        $prototype = new Prototype(ConcreteStub::class);

        $container = $this->createMock(Container::class);

        $this->assertInstanceOf(ConcreteStub::class, $prototype->provide($container));
    }

    /**
     * @covers ::__construct()
     * @covers ::bindConstructorFactory()
     * @covers ::provide()
     */
    public function testProvideWithConstructorFactory(): void
    {
        $container = $this->createMock(Container::class);

        $container->method('get')
                  ->willReturnOnConsecutiveCalls(
                      new StaticStub(),
                      new ConcreteStub(),
                  );

        $prototype = (new Prototype(SimpleStub::class))
            ->bindConstructorFactory(StaticStub::class, 'get');

        $instance = $prototype->provide($container);
        $this->assertInstanceOf(SimpleStub::class, $instance);
    }

    /** @covers ::__construct() */
    public function testConstructorWithInvalidClass(): void
    {
        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('Expected string to be a class name. Got "NonExistingClass');

        new Prototype('NonExistingClass');
    }
}
