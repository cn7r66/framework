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
use Vivarium\Assertion\Numeric\IsLessOrEqualThan;

/** @coversDefaultClass \Vivarium\Assertion\Numeric\IsLessOrEqualThan */
final class IsLessOrEqualThanTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(int|float $test, int|float $limit): void
    {
        static::expectNotToPerformAssertions();

        (new IsLessOrEqualThan($limit))
            ->assert($test);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(int|float|string $test, int|float $limit, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsLessOrEqualThan($limit))
            ->assert($test);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(int|float $test, int|float $limit): void
    {
        static::assertTrue(
            (new IsLessOrEqualThan($limit))($test),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(int|float $test, int|float $limit): void
    {
        static::assertFalse(
            (new IsLessOrEqualThan($limit))($test),
        );
    }

    /** @return array<array<int|float>> */
    public static function provideSuccess(): array
    {
        return [
            [10, 10],
            [10, 11],
            [10, 42],
        ];
    }

    /** @return array<array{0:int|float, 1:int|float, 2:string}> */
    public static function provideFailure(): array
    {
        return [
            [10, 3, 'Expected number to be less or equal than 3. Got 10.'],
            [10, 9.99, 'Expected number to be less or equal than 9.99. Got 10.'],
        ];
    }

    /** @return array<array{0:string, 1:int|float, 2:string}> */
    public static function provideInvalid(): array
    {
        return [
            ['String', 10, 'Expected value to be either integer or float. Got string.'],
        ];
    }
}
