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
}
