<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Exception\BindingNotFound;
use Vivarium\Container\Exception\ParameterNotFound;
use Vivarium\Container\Exception\ParameterNotSolvable;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\Constructor;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\DefaultArgumentNotSolvableStub;
use Vivarium\Test\Container\Stub\DefaultArgumentStub;
use Vivarium\Test\Container\Stub\NotSolvableStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\Stub;

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
     * @covers Vivarium\Container\Reflection\BaseMethod::solveParameter()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::invoke()
     */
    public function testInvokeWithParameterResolution(): void
    {
        $constructor = (new Constructor(SimpleStub::class))
                                ->bindParameter('stub')
                                ->to(ConcreteStub::class);

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo(new TypeBinding(ConcreteStub::class)))
                  ->willReturn(new ConcreteStub());

        static::assertInstanceOf(SimpleStub::class, $constructor->invoke($container));
    }

    /**
     * @covers Vivarium\Container\Reflection\BaseMethod::solveParameter()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::invoke()
     */
    public function testInvokeWithParameterAutoResolution(): void
    {
        $constructor = (new Constructor(SimpleStub::class));

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $binding = new TypeBinding(
            Stub::class,
            Binding::DEFAULT,
            SimpleStub::class
        );

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willReturn(new ConcreteStub());

        static::assertInstanceOf(SimpleStub::class, $constructor->invoke($container));
    }

    /**
     * @covers Vivarium\Container\Reflection\BaseMethod::solveParameter()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::invoke()
     */
    public function testInvokeWithDefaultParameterResolution(): void
    {
        $constructor = (new Constructor(DefaultArgumentStub::class));

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $binding = new TypeBinding(
            'string',
            Binding::DEFAULT,
            DefaultArgumentStub::class
        );

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willThrowException(new BindingNotFound($binding));

        $instance = $constructor->invoke($container);

        static::assertInstanceOf(DefaultArgumentStub::class, $instance);
        static::assertSame('DEFAULT', $instance->getString());
    }

    /**
     * @covers Vivarium\Container\Reflection\BaseMethod::solveParameter()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::invoke()
     */
    public function testInvokeWithDefaultNotSolvableParameter(): void
    {
        $constructor = (new Constructor(DefaultArgumentNotSolvableStub::class));

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $container->expects(static::never())
                  ->method('get');

        $instance = $constructor->invoke($container);

        static::assertInstanceOf(DefaultArgumentNotSolvableStub::class, $instance);
        static::assertSame('DEFAULT', $instance->getValue());
    }

    /**
     * @covers Vivarium\Container\Reflection\BaseMethod::solveParameter()
     * @covers ::__construct()
     * @covers ::bindParameter()
     * @covers ::getArguments()
     * @covers ::getArgumentsValue()
     * @covers ::invoke()
     */
    public function testInvokeWithNotSolvableParameter(): void
    {
        static::expectException(ParameterNotSolvable::class);
        static::expectExceptionMessage('Parameter named stub in method __construct is not solvable.');

        $constructor = (new Constructor(NotSolvableStub::class));

        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $constructor->invoke($container);
    }
    /** @covers ::getParameter() */
    public function testGetParameterException(): void
    {
        static::expectException(ParameterNotFound::class);
        static::expectExceptionMessage('Parameter named stub in method __construct not found.');

        $constructor = new Constructor(SimpleStub::class);
        $constructor->getParameter('stub');
    }

    /**
     * @covers ::__construct()
     * @covers ::getArguments()
     */
    public function testInvokeWithoutExplicitConstructor(): void
    {
        $constructor = new Constructor(stdClass::class);

        static::assertSame([], $constructor->getArguments()->toArray());
    }
}
