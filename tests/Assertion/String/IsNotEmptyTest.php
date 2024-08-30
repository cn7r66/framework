<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Test\Assertion\String;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsNotEmpty;

/** @coversDefaultClass \Vivarium\Assertion\String\IsNotEmpty */
final class IsNotEmptyTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $string): void
    {
        static::expectNotToPerformAssertions();

        (new IsNotEmpty())
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideNonValid()
     */
    public function testAssertException(string|int $string, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsNotEmpty())
            ->assert($string);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $string): void
    {
        static::assertTrue((new IsNotEmpty())($string));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $string): void
    {
        static::assertFalse((new IsNotEmpty())($string));
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['Foo'],
            ['A'],
            ['Hello World'],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            ['', 'Expected string to be not empty.'],
            [' ', 'Expected string to be not empty.'],
            ['       ', 'Expected string to be not empty.'],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideNonValid(): array
    {
        return [
            [42, 'Expected value to be string. Got int.'],
        ];
    }
}
