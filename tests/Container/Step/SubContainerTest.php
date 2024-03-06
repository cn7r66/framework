<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Step;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Step\SubContainer;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass Vivarium\Container\Step\SubContainer */
final class SubContainerTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::solve()
     */
    public function testSolveNotFound(): void
    {
        $container = $this->getMockBuilder(ContainerInterface::class)
                          ->getMock();

        $container->expects(static::once())
                  ->method('has')
                  ->willReturn(false);

        $container->expects(static::never())
                  ->method('get');

        $step = new SubContainer($container);

        $provider = $step->solve(new SimpleBinding('theId'), static function () {
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

        $provider = $step->solve($binding, static function (): void {
            throw new RuntimeException();
        });

        static::assertInstanceOf(Instance::class, $provider);
    }
}
