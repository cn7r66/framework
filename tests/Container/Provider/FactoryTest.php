<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Provider\Factory;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\StubFactory;
use Vivarium\Container\Container;
use PHPUnit\Framework\MockObject\MockObject;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Test\Assertion\Stub\Stub;

/** @coversDefaultClass Vivarium\Container\Provider\Factory */
final class FactoryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::invoke()
     */
    public function testProvide(): void
    {
        $factory = (new Factory(StubFactory::class, 'create'))
                        ->bindParameter('stub')
                        ->to(ConcreteStub::class);

        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $factoryBinding = new ClassBinding(StubFactory::class);
        $stubBinding    = new TypeBinding(ConcreteStub::class);

        $container->expects(static::exactly(2))
                  ->method('get')
                  ->willReturnOnConsecutiveCalls(
                    new StubFactory(),
                    new ConcreteStub()
                  );

        static::assertInstanceOf(SimpleStub::class, $factory->provide($container));
    }
}
