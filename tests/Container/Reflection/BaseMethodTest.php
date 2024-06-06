<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Reflection;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Reflection\BaseMethod;
use Vivarium\Equality\Equal;
use Vivarium\Test\Container\Stub\BaseStub;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass \Vivarium\Container\Reflection\BaseMethod */
final class BaseMethodTest extends TestCase
{
    /**
     * @covers ::equals
     * @covers ::hash
     */
    public function testEquality(): void
    {
        $method1 = $this->getMockBuilder(BaseMethod::class)
                        ->setConstructorArgs([
                            ConcreteStub::class,
                            'do',
                        ])
                        ->onlyMethods([])
                        ->getMock();

        $method2 = $this->getMockBuilder(BaseMethod::class)
                        ->setConstructorArgs([
                            BaseStub::class,
                            'do',
                        ])
                        ->onlyMethods([])
                        ->getMock();

        static::assertTrue(Equal::areEquals($method1, $method1));
        static::assertSame($method1->hash(), $method1->hash());

        static::assertFalse(Equal::areEquals($method1, $method2));
        static::assertNotSame($method1->hash(), $method2->hash());

        static::assertFalse(Equal::areEquals($method1, new stdClass()));
    }
}
