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
use Vivarium\Assertion\Var\IsFloat;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsFloat */
final class IsFloatTest extends TestCase
{
        /**
         * @covers ::assert()
         * @dataProvider provideSuccess()
         */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsFloat())
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

        (new IsFloat())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsFloat())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsFloat())($var),
        );
    }

    /** @return array<array<float>> */
    public static function provideSuccess(): array
    {
        return [
            [4.5],
            [4.0],
            [4.999999],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [42, 'Expected value to be float. Got integer.'],
            ['string', 'Expected value to be float. Got string.'],
        ];
    }
}
