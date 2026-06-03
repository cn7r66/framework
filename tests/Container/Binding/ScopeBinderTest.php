<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ScopeBinder;
use Vivarium\Container\Scope;

/** @coversDefaultClass \Vivarium\Container\Binding\ScopeBinder */
final class ScopeBinderTest extends TestCase
{
    private function makeBinder(): ScopeBinder
    {
        return new ScopeBinder(static function (Scope $scope): Scope {
            return $scope;
        });
    }

    /** @covers ::transient */
    public function testTransientReturnsTransientScope(): void
    {
        static::assertSame(Scope::TRANSIENT, $this->makeBinder()->transient());
    }

    /** @covers ::clonable */
    public function testClonableReturnsCloneableScope(): void
    {
        static::assertSame(Scope::CLONEABLE, $this->makeBinder()->clonable());
    }

    /** @covers ::service */
    public function testServiceReturnsServiceScope(): void
    {
        static::assertSame(Scope::SERVICE, $this->makeBinder()->service());
    }
}
