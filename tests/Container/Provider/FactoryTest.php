<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Reflection\FactoryMethodCall;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StubFactory;

/** @coversDefaultClass Vivarium\Container\Provider\Factory */
final class FactoryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::configure()
     */
    public function testProvide(): void
    {
        $factory = (new Factory(StubFactory::class, 'create'))
                        ->configure(function (FactoryMethodCall $method) {
                            return $method
                                ->bindParameter('stub')
                                ->to(ConcreteStub::class);
                        });
                        
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $factoryBinding = new ClassBinding(StubFactory::class);
        $stubBinding    = new TypeBinding(ConcreteStub::class);

        $container->expects(static::exactly(2))
                  ->method('get')
                  ->willReturnOnConsecutiveCalls(
                      new StubFactory(),
                      new ConcreteStub(),
                  );

        static::assertInstanceOf(SimpleStub::class, $factory->provide($container));
    }
}
