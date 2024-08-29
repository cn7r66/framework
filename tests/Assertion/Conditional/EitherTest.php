<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Conditional;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Numeric\IsGreaterThan;
use Vivarium\Assertion\Numeric\IsInClosedRange;
use Vivarium\Assertion\Type\IsClassOrInterface;
use Vivarium\Assertion\Var\IsInteger;
use Vivarium\Assertion\Var\IsObject;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Test\Assertion\Stub\StubClass;

/** @coversDefaultClass \Vivarium\Assertion\Conditional\Either */
final class EitherTest extends TestCase
{
    /**
     * @param Assertion<mixed> $assertion1
     * @param Assertion<mixed> $assertion2
     *
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::safeAssert()
     *
     * @dataProvider provideSuccess()
     */
    public function testAssert(Assertion $assertion1, Assertion $assertion2, mixed $value): void
    {
        static::expectNotToPerformAssertions();

        (new Either(
            $assertion1,
            $assertion2,
        ))->assert($value);
    }

    /**
     * @param Assertion<mixed> $assertion1
     * @param Assertion<mixed> $assertion2
     *
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::safeAssert()
     *
     * @dataProvider provideFailure()
     */
    public function testAssertException(Assertion $assertion1, Assertion $assertion2, mixed $value): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Failed all assertions in either condition.');

        (new Either(
            $assertion1,
            $assertion2,
        ))->assert($value);
    }

    /**
     * @param Assertion<mixed> $assertion1
     * @param Assertion<mixed> $assertion2
     *
     * @covers ::__construct()
     * @covers ::__invoke()
     * @covers ::safeAssert()
     *
     * @dataProvider provideSuccess()
     */
    public function testInvoke(Assertion $assertion1, Assertion $assertion2, mixed $value): void
    {
        static::assertTrue(
            (new Either(
                $assertion1,
                $assertion2,
            ))($value),
        );
    }

    /**
     * @param Assertion<mixed> $assertion1
     * @param Assertion<mixed> $assertion2
     *
     * @covers ::__construct()
     * @covers ::__invoke()
     * @covers ::safeAssert()
     *
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(Assertion $assertion1, Assertion $assertion2, mixed $value): void
    {
        static::assertFalse(
            (new Either(
                $assertion1,
                $assertion2,
            ))($value),
        );
    }

    /** @return array<array{0:Assertion<mixed>, 1:Assertion<mixed>, 2:int|object}> */
    public static function provideSuccess(): array
    {
        return [
            [
                new IsGreaterThan(100),
                new IsInClosedRange(0, 9),
                6,
            ],
            [
                new IsString(),
                new IsInteger(),
                42,
            ],
            [
                new IsGreaterThan(5),
                new IsInClosedRange(40, 50),
                42,
            ],
            [
                new IsClassOrInterface(),
                new IsObject(),
                new StubClass(),
            ],
        ];
    }

    /** @return array<array{0:Assertion<mixed>, 1:Assertion<mixed>, 2:int|object}> */
    public static function provideFailure(): array
    {
        return [
            [
                new IsGreaterThan(100),
                new IsInClosedRange(0, 9),
                42,
            ],
        ];
    }
}
