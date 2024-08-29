<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Conditional;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Conditional\All;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\Contains;
use Vivarium\Assertion\String\IsLongAtLeast;
use Vivarium\Assertion\Var\IsString;

/** @coversDefaultClass \Vivarium\Assertion\Conditional\All */
final class AllTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new All(
            new IsString(),
            new IsLongAtLeast(5),
        ))->assert('Hello');
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be long at least 10. Got 6.');

        (new All(
            new IsLongAtLeast(10),
            new Contains('Random'),
        ))->assert('Random');
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssertFailLater(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be long at least 5. Got 2.');

        (new All(
            new IsString(),
            new IsLongAtLeast(5),
        ))->assert('Hi');
    }

    /** @covers ::__invoke() */
    public function testInvoke(): void
    {
        $assertion = (new All(
            new IsString(),
            new IsLongAtLeast(5),
        ));

        static::assertTrue($assertion('Hello'));
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     */
    public function testInvokeFail(): void
    {
        $assertion = (new All(
            new IsString(),
            new IsLongAtLeast(5),
        ));

        static::assertFalse($assertion(7));
    }
}
