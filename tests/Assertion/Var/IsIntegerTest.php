<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Var;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsInteger;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsInteger */
final class IsIntegerTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsInteger())
            ->assert($var);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     */
    public function testAssertException(mixed $var, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsInteger())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsInteger())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsInteger())($var),
        );
    }

    /** @return array<array<int>> */
    public static function provideSuccess(): array
    {
        return [
            [1],
            [0],
            [-1],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [42.0, 'Expected value to be integer. Got double.'],
            [0.99999, 'Expected value to be integer. Got double.'],
            ['string', 'Expected value to be integer. Got string.'],
        ];
    }
}
