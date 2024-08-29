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
use Vivarium\Assertion\String\IsLongAtMax;

/** @coversDefaultClass \Vivarium\Assertion\String\IsLongAtMax */
final class IsLongAtMaxTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string, int $length, string $encoding): void
    {
        static::expectNotToPerformAssertions();

        (new IsLongAtMax($length, $encoding))
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideNonValid()
     */
    public function testAssertException(string|int $string, int $length, string $encoding, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsLongAtMax($length, $encoding))
            ->assert($string);
    }

        /**
         * @covers ::__construct()
         * @covers ::__invoke()
         * @dataProvider provideSuccess()
         */
    public function testInvoke(string $string, int $length, string $encoding): void
    {
        static::assertTrue((new IsLongAtMax($length, $encoding))($string));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string, int $length, string $encoding, string $message): void
    {
        static::assertFalse((new IsLongAtMax($length, $encoding))($string));
    }

    /** @return array<array{0:string, 1:int, 2:string}> */
    public static function provideSuccess(): array
    {
        return [
            ['Hello', 6, 'UTF-8'],
            ['Hello', 5, 'UTF-8'],
            ['ππ', 2, 'UTF-8'],
        ];
    }

    /** @return array<array{0:string, 1:int, 2:string, 3:string}> */
    public static function provideFailure(): array
    {
        return [
            ['Hello World', 5, 'UTF-8', 'Expected string to be long at max 5. Got 11'],
            ['ππ', 1, 'UTF-8', 'Expected string to be long at max 1. Got 2.'],
        ];
    }

    /** @return array<array{0:int|string, 1:int, 2:string, 3:string}> */
    public static function provideNonValid(): array
    {
        return [
            [42, 5, 'UTF-8', 'Expected value to be string. Got integer.'],
            ['Hello World', 3, 'Foo', '"Foo" is not a valid system encoding.'],
            ['Hello World', 0, 'UTF-8', 'Expected number to be greater than 0. Got 0.'],
        ];
    }
}
