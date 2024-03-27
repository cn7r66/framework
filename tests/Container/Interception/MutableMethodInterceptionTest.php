<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Interception;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Interception\ImmutableMethodInterception;
use Vivarium\Container\Interception\MutableMethodInterception;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\ImmutableStub;

/** @coversDefaultClass Vivarium\Container\Interception\MutableMethodInterception */
final class MutableMethodInterceptionTest extends TestCase
{
    /**
     * @covers ::__construct() 
     * @covers ::intercept() 
     */
    public function testIntercept(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $interception = new MutableMethodInterception(
                            (new MethodCall(ConcreteStub::class, 'setInt'))
                                ->bindParameter('n')
                                ->toInstance(42)
                            );

        $stub  = new ConcreteStub();
        $stub1 = $interception->intercept($container, $stub);

        static::assertSame($stub, $stub1);
        static::assertSame(42, $stub1->getInt());
    }

    /**
     * @covers ::__construct() 
     * @covers ::intercept() 
     * @covers ::configure()
     */
    public function testConfigure(): void
    {
        $container = $this->getMockBuilder(Container::class)->getMock();

        $interception = new MutableMethodInterception(
                            new MethodCall(ConcreteStub::class, 'setInt')
                        );

        $interception = $interception->configure(static function (MethodCall $method) {
            return $method->bindParameter('n')
                          ->toInstance(42);
        });

        $stub  = new ConcreteStub();
        $stub1 = $interception->intercept($container, $stub);

        static::assertSame($stub, $stub1);
        static::assertSame(42, $stub1->getInt());
    }
}
