<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Scope;

/** @coversDefaultClass \Vivarium\Container\Binding\ScopeBinder */
final class ScopeBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::service()
     */
    public function testService(): void
    {
        $binder = new ScopeBinder(static function (Scope $scope): void {
            static::assertSame(Scope::SERVICE, $scope);
        });

        $binder->service();
    }

    /**
     * @covers ::__construct()
     * @covers ::cloneable()
     */
    public function testCloneable(): void
    {
        $binder = new ScopeBinder(static function (Scope $scope): void {
            static::assertSame(Scope::CLONEABLE, $scope);
        });

        $binder->cloneable();
    }

    /**
     * @covers ::__construct()
     * @covers ::prototype()
     */
    public function testPrototype(): void
    {
        $binder = new ScopeBinder(static function (Scope $scope): void {
            static::assertSame(Scope::PROTOTYPE, $scope);
        });

        $binder->prototype();
    }
}
