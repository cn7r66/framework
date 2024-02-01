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
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass Vivarium\Container\Provider\Cloneable */
final class CloneableTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvide(): void
    {
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

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
