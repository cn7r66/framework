<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Conditional;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Conditional\Not;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsString;

/** @coversDefaultClass \Vivarium\Assertion\Conditional\Not */
final class NotTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new Not(new IsString()))
            ->assert(42);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Failed negating the assertion "Vivarium\Assertion\Var\IsString" with value "Hello World".',
        );

        (new Not(new IsString()))
            ->assert('Hello World');
    }

    /** @covers ::__invoke() */
    public function testInvoke(): void
    {
        static::assertTrue((new Not(new IsString()))(42));
        static::assertFalse((new Not(new IsString()))('Hello World'));
    }
}
