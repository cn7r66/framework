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
use Psr\Container\NotFoundExceptionInterface;
use Vivarium\Container\Binding;
use Vivarium\Container\Container;
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
        $binding = $this->createMock(Binding::class);

        $container = $this->createMock(Container::class);
        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willReturn(1);

        $fallback = new Fallback($binding, 2);

        static::assertSame(1, $fallback->provide($container));
    }

        /**
         * @covers ::__construct()
         * @covers ::provide()
         */
    public function testProvideFallback(): void
    {
        $binding = $this->createMock(Binding::class);

        $exception = $this->createMock(NotFoundExceptionInterface::class);

        $container = $this->createMock(Container::class);
        $container->expects(static::once())
                  ->method('get')
                  ->with(static::equalTo($binding))
                  ->willThrowException($exception);

        $fallback = new Fallback($binding, 2);

        static::assertSame(2, $fallback->provide($container));
    }
}
