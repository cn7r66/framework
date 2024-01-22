<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\SubContainer;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Instance;

/** @coversDefaultClass Vivarium\Container\SubContainer */
final class SubContainerTest extends TestCase
{
    /**
     * @covers ::__construct()  
     * @covers ::solve() 
     */
    public function testSolveNotFound(): void
    {
        /** @var MockObject&ContainerInterface */
        $container = $this->getMockBuilder(ContainerInterface::class)
                          ->getMock();

        $container->expects(static::once())
                  ->method('has')
                  ->willReturn(false);

        $container->expects(static::never())
                  ->method('get');

        $step = new SubContainer($container);

        $provider = $step->solve(new SimpleBinding('theId'), function () {
            return new Prototype(ConcreteStub::class);
        });

        static::assertInstanceOf(Provider::class, $provider);
    }

    /**
     * @covers ::__construct() 
     * @covers ::solve() 
     */
    public function testSolve(): void
    {
        $binding = new SimpleBinding(ConcreteStub::class);

        /** @var MockObject&ContainerInterface */
        $container = $this->getMockBuilder(ContainerInterface::class)
                          ->getMock();

        $container->expects(static::once())
                  ->method('has')
                  ->with(static::equalTo($binding->getId()))
                  ->willReturn(true);

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding->getId()))
                  ->willReturn(new ConcreteStub());

        $step = new SubContainer($container);

        $provider = $step->solve($binding, function () {
            throw new RuntimeException();
        });

        static::assertInstanceOf(Instance::class, $provider);
    }
}
