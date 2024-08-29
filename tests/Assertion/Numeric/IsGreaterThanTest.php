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
use Vivarium\Assertion\Numeric\IsGreaterThan;

/** @coversDefaultClass \Vivarium\Assertion\Numeric\IsGreaterThan */
final class IsGreaterThanTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(int|float $test, int|float $limit): void
    {
        static::expectNotToPerformAssertions();

        (new IsGreaterThan($limit))
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

        (new IsGreaterThan($limit))
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
            (new IsGreaterThan($limit))($test),
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
            (new IsGreaterThan($limit))($test),
        );
    }

    /** @return array<array<int|float>> */
    public static function provideSuccess(): array
    {
        return [
            [11, 10],
            [42, 10],
        ];
    }

    /** @return array<array{0:int|float, 1:int|float, 2:string}> */
    public static function provideFailure(): array
    {
        return [
            [3, 10, 'Expected number to be greater than 10. Got 3.'],
            [9.99, 10, 'Expected number to be greater than 10. Got 9.99.'],
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
