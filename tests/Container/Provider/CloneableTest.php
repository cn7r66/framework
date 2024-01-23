<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Provider;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Container\Provider\Cloneable;
use PHPUnit\Framework\MockObject\MockObject;
use Vivarium\Container\Container;

/** @coversDefaultClass Vivarium\Container\Provider\Cloneable */
final class CloneableTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvide(): void
    {
        /** @var MockObject&Container */
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        /** @var MockObject&Provider */
        $provider = $this->getMockBuilder(Provider::class)
                         ->getMock();

        $provider->expects(static::once())
                 ->method('provide')
                 ->with(static::equalTo($container))
                 ->willReturn(new ConcreteStub());

        $cloneable = new Cloneable($provider);

        $call1 = $cloneable->provide($container);
        $call2 = $cloneable->provide($container);

        static::assertInstanceOf(ConcreteStub::class, $call1);
        static::assertInstanceOf(ConcreteStub::class, $call2);
        static::assertNotSame($call1, $call2);
    }
}
