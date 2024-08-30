<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Boolean;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Boolean\IsFalse;
use Vivarium\Assertion\Exception\AssertionFailed;

/** @coversDefaultClass \Vivarium\Assertion\Boolean\IsFalse */
final class IsFalseTest extends TestCase
{
    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new IsFalse())
            ->assert(false);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected boolean to be false. Got true');

        (new IsFalse())
            ->assert(true);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertWithoutBoolean(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected value to be boolean. Got int.');

        (new IsFalse())
            ->assert(42);
    }
}
