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
use Vivarium\Assertion\String\IsEmpty;

/** @coversDefaultClass \Vivarium\Assertion\String\IsEmpty */
final class IsEmptyTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string): void
    {
        static::expectNotToPerformAssertions();

        (new IsEmpty())
            ->assert($string);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     * @dataProvider provideNonString()
     */
    public function testAssertException(string|int $string, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsEmpty())
            ->assert($string);
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $string): void
    {
        static::assertTrue((new IsEmpty())($string));
    }

    /**
     * @covers ::assert()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string): void
    {
        static::assertFalse((new IsEmpty())($string));
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            [''],
            ['        '],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            ['Hello World', 'Expected string to be empty. Got "Hello World".'],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideNonString(): array
    {
        return [
            [42, 'Expected value to be string. Got int.'],
        ];
    }
}
