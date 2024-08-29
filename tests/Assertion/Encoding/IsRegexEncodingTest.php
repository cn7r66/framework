<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Encoding;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Encoding\IsRegexEncoding;
use Vivarium\Assertion\Exception\AssertionFailed;

/** @coversDefaultClass \Vivarium\Assertion\Encoding\IsRegexEncoding */
final class IsRegexEncodingTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $encoding): void
    {
        static::expectNotToPerformAssertions();

        (new IsRegexEncoding())->assert($encoding);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string|int $encoding, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsRegexEncoding())->assert($encoding);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $encoding): void
    {
        static::assertTrue(
            (new IsRegexEncoding())($encoding),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string|int $encoding): void
    {
        static::assertFalse(
            (new IsRegexEncoding())($encoding),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['UTF-8'],
            ['UTF-32'],
            ['ASCII'],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            ['Windows-1251', '"Windows-1251" is not a valid regex encoding.'],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideInvalid(): array
    {
        return [
            [42, 'Expected value to be string. Got integer.'],
        ];
    }
}
