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
use Vivarium\Assertion\Var\IsArray;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsArray */
final class IsArrayTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsArray())
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

        (new IsArray())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsArray())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsArray())($var),
        );
    }

    /** @return array<array<array<scalar>>> */
    public static function provideSuccess(): array
    {
        return [
            [[]],
            [[1, 2, 3]],
            [['a', 'b', 'c']],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [42, 'Expected value to be array. Got int.'],
            ['string', 'Expected value to be array. Got string.'],
        ];
    }
}
