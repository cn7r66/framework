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
use Vivarium\Container\Binding;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass Vivarium\Container\Provider\ContainerCall */
final class ContainerCallTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvide(): void
    {
        /** @var MockObject&Binding */
        $binding = $this->getMockBuilder(Binding::class)
                        ->getMock();

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willReturn(new ConcreteStub());

        $containerCall = new ContainerCall($binding);

        static::assertInstanceOf(ConcreteStub::class, $containerCall->provide($container));
    }
}
