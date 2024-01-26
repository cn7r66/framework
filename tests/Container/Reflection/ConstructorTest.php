<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Container\Reflection\Constructor;
use Vivarium\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Exception\ParameterNotFound;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Container\Provider;

/** @coversDefaultClass Vivarium\Container\Reflection\Constructor */
final class ConstructorTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::invoke()
     */
    public function testInvoke(): void
    {
        $constructor = new Constructor(ConcreteStub::class);

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $container->expects(static::never())
                  ->method('get');

        static::assertInstanceOf(ConcreteStub::class, $constructor->invoke($container));
    }

    /** @covers ::getName() */
    public function testGetName(): void
    {
        $constructor = new Constructor(ConcreteStub::class);

        static::assertSame('__construct', $constructor->getName());
    }

    /**
     * @covers ::bindParameter()
     * @covers ::hasParameter()
     * @covers ::getParameter()
     */
    public function testBindParameter(): void
    {
        $constructor = (new Constructor(SimpleStub::class))
                                    ->bindParameter('stub')
                                    ->to(ConcreteStub::class);

        static::assertTrue($constructor->hasParameter('stub'));
        static::assertInstanceOf(Provider::class, $constructor->getParameter('stub'));
    }

    /**
     * @covers Vivarium\Container\Reflection\BaseMethod::__construct()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::solveParameter()
     * @covers ::invoke()
     */
    public function testInvokeWithParameterResolution(): void
    {
        $constructor = (new Constructor(SimpleStub::class))
        ->bindParameter('stub')
        ->to(ConcreteStub::class);

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
        ->getMock();

        $container->expects(static::once())
        ->method('get')
        ->with(static::equalTo(new TypeBinding(ConcreteStub::class)))
        ->willReturn(new ConcreteStub());

        static::assertInstanceOf(SimpleStub::class, $constructor->invoke($container));
    }

    /**
     * @covers ::getParameter()
     */
    public function testGetParameterException(): void
    {
        static::expectException(ParameterNotFound::class);
        static::expectExceptionMessage('Parameter named stub in method __construct not found.');

        $constructor = new Constructor(SimpleStub::class);
        $constructor->getParameter('stub');
    }
}
