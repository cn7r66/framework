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
use Vivarium\Assertion\Var\IsString;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsString */
final class IsStringTest extends TestCase
{
        /**
         * @covers ::assert()
         * @dataProvider provideSuccess()
         */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsString())
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

        (new IsString())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsString())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsString())($var),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            [''],
            ['Hello World'],
            ['a'],
            ['42'],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [[], 'Expected value to be string. Got array.'],
            [42, 'Expected value to be string. Got int.'],
        ];
    }
}
