<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Binding\MethodBinder;
use Vivarium\Container\Method;

/** @coversDefaultClass \Vivarium\Container\Binding\MethodBinder */
final class MethodBinderTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::method
     */
    public function testMethodForwardsMethodNameToCreate(): void
    {
        $capturedMethod = null;

        $binder = new MethodBinder(
            static function (string $method, callable $configure) use (&$capturedMethod): string {
                $capturedMethod = $method;

                return 'result';
            },
        );

        $binder->method('setFoo');

        static::assertSame('setFoo', $capturedMethod);
    }

    /** @covers ::method */
    public function testMethodWithoutConfigureUsesIdentityFunction(): void
    {
        $methodMock     = $this->createMock(Method::class);
        $capturedConfig = null;

        $binder = new MethodBinder(
            static function (string $method, callable $configure) use (&$capturedConfig): string {
                $capturedConfig = $configure;

                return 'result';
            },
        );

        $binder->method('setFoo');

        static::assertSame($methodMock, ($capturedConfig)($methodMock));
    }

    /** @covers ::method */
    public function testMethodWithConfigureForwardsCallbackToCreate(): void
    {
        $configure      = static fn (Method $m): Method => $m;
        $capturedConfig = null;

        $binder = new MethodBinder(
            static function (string $method, callable $cfg) use (&$capturedConfig): string {
                $capturedConfig = $cfg;

                return 'result';
            },
        );

        $binder->method('setFoo', $configure);

        static::assertSame($configure, $capturedConfig);
    }

    /** @covers ::method */
    public function testMethodReturnsValueFromCreate(): void
    {
        $expected = new stdClass();

        $binder = new MethodBinder(
            static fn (string $method, callable $configure): object => $expected,
        );

        static::assertSame($expected, $binder->method('setFoo'));
    }
}
