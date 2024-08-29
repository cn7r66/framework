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
use Vivarium\Assertion\String\StartsWith;

/** @coversDefaultClass \Vivarium\Assertion\String\StartsWith */
final class StartsWithTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string, string $start): void
    {
        static::expectNotToPerformAssertions();

        (new StartsWith($start))
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideNonString()
     */
    public function testAssertException(string|int $string, string $start, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new StartsWith($start))
            ->assert($string);
    }

        /**
         * @covers ::__construct()
         * @covers ::__invoke()
         * @dataProvider provideSuccess()
         */
    public function testInvoke(string $string, string $start): void
    {
        static::assertTrue((new StartsWith($start))($string));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string, string $start, string $message): void
    {
        static::assertFalse((new StartsWith($start))($string));
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['Hello World', 'Hello'],
            ['Hello World', 'H'],
            ['Hello World', 'Hello W'],
            ['Hello World', 'Hello '],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            ['Hello World', 'World', 'Expected that string "Hello World" starts with "World".'],
        ];
    }

    /** @return array<array{0:int, 1:string, 2:string}> */
    public static function provideNonString(): array
    {
        return [
            [42, 'H', 'Expected value to be string. Got integer.'],
        ];
    }
}
