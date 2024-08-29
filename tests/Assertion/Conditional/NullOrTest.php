<?php

/**
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2020 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Conditional;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Conditional\NullOr;
use Vivarium\Assertion\Var\IsArray;
use Vivarium\Assertion\Var\IsInteger;
use Vivarium\Assertion\Var\IsString;

/** @coversDefaultClass \Vivarium\Assertion\Conditional\NullOr */
final class NullOrTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new NullOr(new IsString()))->assert('Hello World');
        (new NullOr(new IsString()))->assert(null);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     */
    public function testAssertException(): void
    {
        static::expectException(InvalidArgumentException::class);
        static::expectExceptionMessage('Expected value to be string. Got array.');

        (new NullOr(new IsString()))->assert([]);
    }

    /** @covers ::__invoke() */
    public function testInvoke(): void
    {
        static::assertTrue((new NullOr(new IsInteger()))(null));
        static::assertTrue((new NullOr(new IsArray()))([]));
        static::assertTrue((new NullOr(new IsInteger()))(42));
        static::assertFalse((new NullOr(new IsString()))(42));
        static::assertFalse((new NullOr(new IsString()))([]));
    }
}
