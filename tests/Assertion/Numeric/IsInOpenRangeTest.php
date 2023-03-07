<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Numeric;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Numeric\IsInOpenRange;

/** @coversDefaultClass \Vivarium\Assertion\Numeric\IsInOpenRange */
final class IsInOpenRangeTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();



        (new IsInOpenRange(0, 9))
            ->assert(5);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     */
    public function testInvoke(): void
    {
        static::assertFalse((new IsInOpenRange(0, 9))(0));
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected number to be in open range (0, 9). Got 9.');

        (new IsInOpenRange(0, 9))
            ->assert(9);
    }

    /** @covers ::assert() */
    public function testAssertWithWrongRange(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Lower bound must be lower than upper bound. Got (10, 0).');

        (new IsInOpenRange(10, 0))
            ->assert(5);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     */
    public function testAssertWithoutNumeric(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected value to be either integer or float. Got "String".');

        (new IsInOpenRange(0, 10))
            ->assert('String');
    }
}
