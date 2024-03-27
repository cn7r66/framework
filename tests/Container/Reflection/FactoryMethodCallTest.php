<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\FactoryMethodCall;
use Vivarium\Equality\Equal;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StubFactory;

/** @coversDefaultClass \Vivarium\Container\Reflection\FactoryMethodCall */
final class FactoryMethodCallTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::invoke()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     */
    public function testInvoke(): void
    {
        $method = new FactoryMethodCall(
            StubFactory::class,
            'create',
        );

        $method = $method->bindParameter('stub')
                         ->to(ConcreteStub::class);

        $container = $this->createMock(Container::class);

        $container->expects(static::exactly(2))
                  ->method('get')
                  ->willReturnOnConsecutiveCalls(
                      new StubFactory(),
                      new ConcreteStub(),
                  );

        $instance = $method->invoke($container);

        static::assertInstanceOf(SimpleStub::class, $instance);
    }

    /**
     * @covers ::__construct()
     * @covers ::getBinding()
     */
    public function testGetBinding(): void
    {
        $method = new FactoryMethodCall(
            StubFactory::class,
            'create',
        );

        $binding = new ClassBinding(StubFactory::class);

        static::assertTrue(Equal::areEquals($method->getBinding(), $binding));
    }
}
