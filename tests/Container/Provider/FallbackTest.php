<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Exception\BindingNotFound;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Fallback;

/** @coversDefaultClass Vivarium\Container\Provider\Fallback */
final class FallbackTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvide(): void
    {
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $provider = $this->createMock(Provider::class);
        $provider->expects(static::once())
                 ->method('provide')
                 ->with(static::equalTo($container))
                 ->willReturn(1);

        $provider2 = $this->createMock(Provider::class);
        $provider2->expects(static::never())
                  ->method('provide');

        $fallback = new Fallback($provider, $provider2);

        $fallback->provide($container);
    }

        /**
     * @covers ::__construct()
     * @covers ::provide()
     */
    public function testProvideFallback(): void
    {
        $container = $this->getMockBuilder(Container::class)
                          ->getMock();

        $exception = $this->createMock(ContainerExceptionInterface::class);

        $provider = $this->createMock(Provider::class);
        $provider->expects(static::once())
                 ->method('provide')
                 ->with(static::equalTo($container))
                 ->willThrowException($exception);

        $provider2 = $this->createMock(Provider::class);
        $provider2->expects(static::once())
                  ->method('provide')
                  ->with(static::equalTo($container))
                  ->willReturn(1);

        $fallback = new Fallback($provider, $provider2);

        $fallback->provide($container);
    }
}
