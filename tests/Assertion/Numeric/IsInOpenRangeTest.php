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
     * @dataProvider provideSuccess()
     */
    public function testAssert(int|float $number, int|float $min, int|float $max): void
    {
        static::expectNotToPerformAssertions();

        (new IsInOpenRange($min, $max))
        ->assert($number);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(int|float|string $number, int|float $min, int|float $max, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsInOpenRange($min, $max))
            ->assert($number);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(int|float $number, int|float $min, int|float $max): void
    {
        static::assertTrue(
            (new IsInOpenRange($min, $max))($number),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(int|float $number, int|float $min, int|float $max): void
    {
        static::assertFalse(
            (new IsInOpenRange($min, $max))($number),
        );
    }

    /** @return array<array<int|float>> */
    public static function provideSuccess(): array
    {
        return [
            [1, 0, 9],
            [8, 0, 9],
            [5, 0, 9],
        ];
    }

    /** @return array<array{0:int|float, 1:int|float, 2:int|float, 3:string}> */
    public static function provideFailure(): array
    {
        return [
            [10, 0, 9, 'Expected number to be in open range (0, 9). Got 10.'],
            [0, 0, 9, 'Expected number to be in open range (0, 9). Got 0.'],
            [9, 0, 9, 'Expected number to be in open range (0, 9). Got 9.'],
            [9.0001, 0, 9, 'Expected number to be in open range (0, 9). Got 9.0001.'],
        ];
    }

    /** @return array<array{0:int|float|string, 1:int|float, 2:int|float, 3:string}> */
    public static function provideInvalid(): array
    {
        return [
            [5, 10, 0, 'Lower bound must be lower than upper bound. Got (10, 0).'],
            ['String', 0, 10, 'Expected value to be either integer or float. Got string.'],
        ];
    }
}
