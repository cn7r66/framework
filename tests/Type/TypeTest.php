<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Type;

use PHPUnit\Framework\TestCase;
use Vivarium\Test\Assertion\Stub\StubClass;
use Vivarium\Type\Type;

/** @coversDefaultClass \Vivarium\Type\Type */
final class TypeTest extends TestCase
{
    /**
     * @covers ::toLiteral()
     * @dataProvider provideLiterals()
     */
    public function testToLiteral(mixed $value, string $literal): void
    {
        static::assertSame($literal, Type::toLiteral($value));
    }

    /**
     * @covers ::toString()
     * @dataProvider provideStrings()
     */
    public function testToString(mixed $value, string $string): void
    {
        static::assertSame($string, Type::toString($value));
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideLiterals(): array
    {
        return [
            [true, 'true'],
            [false, 'false'],
            [null, 'null'],
            [[], 'array'],
            ['Hello World', '"Hello World"'],
            [new StubClass(), '"' . StubClass::class . '"'],
            [42, '42'],
            [static fn (mixed $a): mixed => $a, '"Closure"'],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideStrings(): array
    {
        return [
            [true, 'bool'],
            [false, 'bool'],
            [42, 'int'],
            [0.99, 'float'],
            [[], 'array'],
            [null, 'null'],
            [new StubClass(), 'object'],
            [static fn (mixed $a): mixed => $a, 'callable'],
        ];
    }
}
