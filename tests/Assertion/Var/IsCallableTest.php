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
use Vivarium\Assertion\Var\IsCallable;
use Vivarium\Assertion\Var\IsInteger;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsCallable */
final class IsCallableTest extends TestCase
{
        /**
         * @covers ::assert()
         * @dataProvider provideSuccess()
         */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsCallable())
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

        (new IsCallable())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsCallable())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsCallable())($var),
        );
    }

    /** @return array<array<callable>> */
    public static function provideSuccess(): array
    {
        return [
            [new IsInteger()],
            [
                static function (): int {
                    return 42;
                },
            ],
            [static fn (int $a): int => $a],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [42, 'Expected value to be callable. Got int.'],
            ['string', 'Expected value to be callable. Got string.'],
        ];
    }
}
