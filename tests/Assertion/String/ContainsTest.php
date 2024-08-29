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
use Vivarium\Assertion\String\Contains;

/** @coversDefaultClass \Vivarium\Assertion\String\Contains */
class ContainsTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string, string $substring): void
    {
        static::expectNotToPerformAssertions();

        (new Contains($substring))
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideNonValid()
     */
    public function testAssertException(string|int $string, string $substring, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new Contains($substring))
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $string, string $substring): void
    {
        static::assertTrue((new Contains($substring))($string));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string, string $substring): void
    {
        static::assertFalse((new Contains($substring))($string));
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['Foo Bar', 'Bar'],
            ['Hello World', 'H'],
            ['Hello World', 'Hello'],
            ['Hello World', 'World'],
            ['Hello World', 'lo Wo'],
            ['Hello World', ' '],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            ['Hello World', 'Foo', 'Expected that string contains "Foo".'],
            ['Hello World', '  ', 'Expected that string contains "  ".'],
        ];
    }

    /** @return array<array{0:int, 1:string, 2:string}> */
    public static function provideNonValid(): array
    {
        return [
            [42, 'Foo', 'Expected value to be string. Got integer.'],
        ];
    }
}
