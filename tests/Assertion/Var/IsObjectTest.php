<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Var;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsObject;
use Vivarium\Test\Assertion\Stub\StubClass;

/** @coversDefaultClass \Vivarium\Assertion\Var\IsObject */
final class IsObjectTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(mixed $var): void
    {
        static::expectNotToPerformAssertions();

        (new IsObject())
            ->assert($var);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     */
    public function testAssertException(mixed $var, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsObject())
            ->assert($var);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(mixed $var): void
    {
        static::assertTrue(
            (new IsObject())($var),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(mixed $var): void
    {
        static::assertFalse(
            (new IsObject())($var),
        );
    }

    /** @return array<array<object>> */
    public static function provideSuccess(): array
    {
        return [
            [new stdClass()],
            [new StubClass()],
        ];
    }

    /** @return array<array{0:mixed, 1:string}> */
    public static function provideFailure(): array
    {
        return [
            [[], 'Expected value to be object. Got array.'],
            [42, 'Expected value to be object. Got int.'],
        ];
    }
}
