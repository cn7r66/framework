<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsClass;
use Vivarium\Test\Assertion\Stub\Stub;
use Vivarium\Test\Assertion\Stub\StubClass;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsClass */
final class IsClassTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type): void
    {
        static::expectNotToPerformAssertions();

        (new IsClass())
            ->assert($type);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string|int $type, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsClass())
            ->assert($type);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type): void
    {
        static::assertTrue(
            (new IsClass())($type),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type): void
    {
        static::assertFalse(
            (new IsClass())($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['stdClass'],
            [StubClass::class],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'NonExistentClass',
                'Expected string to be class name. Got "NonExistentClass".',
            ],
            [
                Stub::class,
                'Expected string to be class name. Got "Vivarium\Test\Assertion\Stub\Stub".',
            ],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideInvalid(): array
    {
        return [
            [42, 'Expected value to be string. Got int.'],
        ];
    }
}
