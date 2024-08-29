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
use Vivarium\Assertion\String\IsLong;

/** @coversDefaultClass \Vivarium\Assertion\String\IsLong */
final class IsLongTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string, int $length, string $encoding): void
    {
        static::expectNotToPerformAssertions();

        (new IsLong($length, $encoding))
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

        (new IsLong($length, $encoding))
            ->assert($string);
    }

        /**
         * @covers ::__construct()
         * @covers ::__invoke()
         * @dataProvider provideSuccess()
         */
    public function testInvoke(string $string, int $length, string $encoding): void
    {
        static::assertTrue(
            (new IsLong($length, $encoding))($string),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string|int $string, int $length, string $encoding): void
    {
        static::assertFalse(
            (new IsLong($length, $encoding))($string),
        );
    }

    /** @return array<array{0:string, 1:int, 2:string}> */
    public static function provideSuccess(): array
    {
        return [
            ['Hello World', 11, 'UTF-8'],
            ['π', 1, 'UTF-8'],
        ];
    }

    /** @return array<array{0:string, 1:int, 2:string, 3:string}> */
    public static function provideFailure(): array
    {
        return [
            ['Hello', 6, 'UTF-8', 'Expected string to be long 6. Got 5.'],
            ['π', 2, 'UTF-8', 'Expected string to be long 2. Got 1.'],
        ];
    }

    /** @return array<array{0:int|string, 1:int, 2:string, 3:string}> */
    public static function provideNonValid(): array
    {
        return [
            ['Hello', 6, 'Foo', '"Foo" is not a valid system encoding.'],
            [42, 3, 'UTF-8', 'Expected value to be string. Got integer.'],
        ];
    }
}
