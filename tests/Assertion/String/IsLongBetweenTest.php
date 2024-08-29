<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\String;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsLongBetween;

/** @coversDefaultClass \Vivarium\Assertion\String\IsLongBetween */
final class IsLongBetweenTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string, int $min, int $max, string $encoding): void
    {
        static::expectNotToPerformAssertions();

        (new IsLongBetween($min, $max, $encoding))
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideNonValid()
     */
    public function testAssertException(string|int $string, int $min, int $max, string $encoding, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsLongBetween($min, $max, $encoding))
            ->assert($string);
    }

        /**
         * @covers ::__construct()
         * @covers ::__invoke()
         * @dataProvider provideSuccess()
         */
    public function testInvoke(string $string, int $min, int $max, string $encoding): void
    {
        static::assertTrue((new IsLongBetween($min, $max, $encoding))($string));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string, int $min, int $max, string $encoding, string $message): void
    {
        static::assertFalse((new IsLongBetween($min, $max, $encoding))($string));
    }

    /** @return array<array{0:string, 1:int, 2:int, 3:string}> */
    public static function provideSuccess(): array
    {
        return [
            ['Hello', 1, 5, 'UTF-8'],
            ['Hi', 2, 5, 'UTF-8'],
            ['ππ', 1, 3, 'UTF-8'],
        ];
    }

    /** @return array<array{0:string, 1:int, 2:int, 3:string, 4:string}> */
    public static function provideFailure(): array
    {
        return [
            ['Hello World', 5, 10, 'UTF-8', 'Expected string to be long between 5 and 10. Got 11'],
            ['ππ', 0, 1, 'UTF-8', 'Expected string to be long between 0 and 1. Got 2.'],
        ];
    }

    /** @return array<array{0:int|string, 1:int, 2:int, 3:string, 4:string}> */
    public static function provideNonValid(): array
    {
        return [
            ['Hello', 3, 5, 'Foo', '"Foo" is not a valid system encoding.'],
            [42, 0, 5, 'UTF-8', 'Expected value to be string. Got integer.'],
        ];
    }
}
