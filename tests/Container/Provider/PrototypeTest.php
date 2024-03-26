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
use Vivarium\Container\Exception\PropertyNotFound;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Reflection\Method;
use Vivarium\Test\Container\Stub\BaseStub;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticStub;
use Vivarium\Test\Container\Stub\StubFactory;

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

        static::assertInstanceOf(ConcreteStub::class, $prototype->provide($container));
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
                      new StubFactory(),
                      new ConcreteStub(),
                  );

        $prototype = (new Prototype(SimpleStub::class))
            ->bindConstructorFactory(StubFactory::class, 'create');

        $instance = $prototype->provide($container);
        static::assertInstanceOf(SimpleStub::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::bindConstructorStaticFactory()
     * @covers ::provide()
     */
    public function testProvideWithConstructorStaticFactory(): void
    {
        $container = $this->createMock(Container::class);

        $container->method('get')
                  ->willReturn(
                      new ConcreteStub(),
                  );

        $prototype = (new Prototype(SimpleStub::class))
            ->bindConstructorStaticFactory(StaticStub::class, 'get');

        $instance = $prototype->provide($container);
        static::assertInstanceOf(SimpleStub::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::provide()
     */
    public function testBindParameter(): void
    {
        $container = $this->createMock(Container::class);

        $container->method('get')
                  ->willReturn(
                      new ConcreteStub(),
                  );

        $prototype = (new Prototype(SimpleStub::class))
            ->bindParameter('stub')
            ->to(ConcreteStub::class);

        $instance = $prototype->provide($container);
        static::assertInstanceOf(SimpleStub::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::bindProperty()
     * @covers ::provide()
     */
    public function testProvideWithProperty(): void
    {
        $container = $this->createMock(Container::class);

        $container->method('get')
            ->willReturn(
                new StubFactory(),
            );

        $prototype = (new Prototype(ConcreteStub::class))
            ->bindProperty('value')
            ->toInstance(420)
            ->bindProperty('factory')
            ->to(StubFactory::class);

        $instance = $prototype->provide($container);

        static::assertSame(420, $instance->value);
        static::assertInstanceOf(StubFactory::class, $instance->factory);
    }

    /**
     * @covers ::__construct()
     * @covers ::bindProperty()
     * @covers ::provide()
     */
    public function testProvideWithNonExistentProperty(): void
    {
        static::expectException(PropertyNotFound::class);
        static::expectExceptionMessage('Property named nonExistent in class Vivarium\Test\Container\Stub\ConcreteStub not found.');

        $container = $this->createMock(Container::class);

        $prototype = (new Prototype(ConcreteStub::class))
            ->bindProperty('nonExistent')
            ->toInstance(42);

        $prototype->provide($container);
    }

    /**
     * @covers ::__construct()
     * @covers ::bindMethod()
     * @covers ::provide()
     */
    public function testProvideWithBindMethod(): void
    {
        $container = $this->createMock(Container::class);

        $container->method('get')
                  ->willReturn(
                      new ConcreteStub(),
                  );

        $prototype = (new Prototype(ConcreteStub::class))
                ->bindMethod('setInt', function(Method $method) {
                    return $method->bindParameter('n')
                                  ->toInstance(42);
                });

        $instance = $prototype->provide($container);
        static::assertInstanceOf(ConcreteStub::class, $instance);
        static::assertSame(42, $instance->getInt());
    }

    /**
     * @covers ::__construct()
     * @covers ::getProperties()
     */
    public function testGetProperties(): void
    {
        $prototype = (new Prototype(ConcreteStub::class))
            ->bindProperty('factory')
            ->to(StubFactory::class)
            ->bindProperty('value')
            ->toInstance(420);

        $properties = $prototype->getProperties();

        static::assertCount(2, $properties);
        static::assertTrue($properties->containsKey('factory'));
        static::assertTrue($properties->containsKey('value'));
    }

    /** @covers ::__construct() */
    public function testConstructorWithInvalidClass(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be a class name. Got "NonExistingClass');

        new Prototype('NonExistingClass');
    }
}
