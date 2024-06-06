<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Interception;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Interception\BaseMethodInterception;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Equality\Equal;
use Vivarium\Test\Container\Stub\BaseStub;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass \Vivarium\Container\Interception\BaseMethodInterception */
final class BaseMethodInterceptionTest extends TestCase
{
    /**
     * @covers ::equals()
     * @covers ::hash()
     */
    public function testEquality(): void
    {
        $method1 = new MethodCall(ConcreteStub::class, 'do');
        $method2 = new MethodCall(BaseStub::class, 'do');
        $method3 = new MethodCall(ConcreteStub::class, 'setInt');

        $interception1 = $this->getMockBuilder(BaseMethodInterception::class)
                              ->setConstructorArgs([$method1])
                              ->onlyMethods(['intercept'])
                              ->getMock();

        $interception2 = $this->getMockBuilder(BaseMethodInterception::class)
                              ->setConstructorArgs([$method2])
                              ->onlyMethods(['intercept'])
                              ->getMock();

        $interception3 = $this->getMockBuilder(BaseMethodInterception::class)
                              ->setConstructorArgs([$method3])
                              ->onlyMethods(['intercept'])
                              ->getMock();

        static::assertTrue(Equal::areEquals($interception1, $interception1));
        static::assertSame($interception1->hash(), $interception1->hash());

        static::assertTrue(Equal::areEquals($interception1, $interception2));
        static::assertSame($interception1->hash(), $interception2->hash());

        static::assertFalse(Equal::areEquals($interception1, $interception3));
        static::assertNotSame($interception1->hash(), $interception3->hash());

        static::assertFalse(Equal::areEquals($interception1, new stdClass()));
    }

    /** @covers ::getMethodCall() */
    public function testGetters(): void
    {
        $method       = new MethodCall(ConcreteStub::class, 'do');
        $interception = $this->getMockBuilder(BaseMethodInterception::class)
                              ->setConstructorArgs([$method])
                              ->onlyMethods(['intercept'])
                              ->getMock();

        static::assertSame($method, $interception->getMethodCall());
    }
}
