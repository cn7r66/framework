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
use Vivarium\Container\Provider\Service;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass Vivarium\Container\Provider\Service */
final class ServiceTest extends TestCase
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

        $service = new Service($provider);

        $call1 = $service->provide($container);
        $call2 = $service->provide($container);

        static::assertSame($call1, $call2);
    }
}
