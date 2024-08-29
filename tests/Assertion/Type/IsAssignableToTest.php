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
use Vivarium\Assertion\Type\IsAssignableTo;

use function array_merge;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsAssignableTo */
final class IsAssignableToTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::getAssertion()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type, string $target): void
    {
        static::expectNotToPerformAssertions();

        (new IsAssignableTo($target))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::getAssertion()
     * @covers ::assert()
     * @dataProvider provideFailure()
     */
    public function testAssertException(string $type, string $target, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsAssignableTo($target))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::getAssertion()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type, string $target): void
    {
        static::assertTrue(
            (new IsAssignableTo($target))($type),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::getAssertion()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type, string $target): void
    {
        static::assertFalse(
            (new IsAssignableTo($target))($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return array_merge(
            IsAssignableToClassTest::provideSuccess(),
            IsAssignableToIntersectionTest::provideSuccess(),
            IsAssignableToPrimitiveTest::provideSuccess(),
            IsAssignableToUnionTest::provideSuccess(),
        );
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return array_merge(
            IsAssignableToClassTest::provideFailure(),
            IsAssignableToIntersectionTest::provideFailure(),
            IsAssignableToPrimitiveTest::provideFailure(),
            IsAssignableToUnionTest::provideFailure(),
        );
    }
}
