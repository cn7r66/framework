<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider\StaticFactory;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StaticStub;

/** @coversDefaultClass Vivarium\Container\Provider\StaticFactory */
final class StaticFactoryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::configure()
     */
    public function testProvide(): void
    {
        $factory = (new StaticFactory(StaticStub::class, 'get'))
                        ->configure(function (CreationalMethod $method) {
                            return $method
                                ->bindParameter('stub')
                                ->to(ConcreteStub::class);
                        });
                        
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $binding    = new TypeBinding(ConcreteStub::class);

        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willReturn(
                    new ConcreteStub()
                  );

        static::assertInstanceOf(SimpleStub::class, $factory->provide($container));
    }
}
