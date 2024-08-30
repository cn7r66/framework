<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Boolean;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Boolean\IsTrue;
use Vivarium\Assertion\Exception\AssertionFailed;

/** @coversDefaultClass \Vivarium\Assertion\Boolean\IsTrue */
final class IsTrueTest extends TestCase
{
    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new IsTrue())
            ->assert(true);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected boolean to be true. Got false.');

        (new IsTrue())
            ->assert(false);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertWithoutBoolean(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected value to be boolean. Got int.');

        (new IsTrue())
            ->assert(42);
    }
}
