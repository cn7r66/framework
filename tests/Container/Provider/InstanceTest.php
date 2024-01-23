<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Vivarium\Container\Provider\Instance;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass Vivarium\Container\Provider\Instance */
final class InstanceTest extends TestCase
{
    /** 
     * @covers ::__construct
     * @covers ::provide() 
     */
    public function testProvide(): void
    {
        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $container->expects(static::never())
                  ->method('get');

        $stub = new ConcreteStub();

        $instance = new Instance($stub);

        static::assertSame($stub, $instance->provide($container));
    }
}
