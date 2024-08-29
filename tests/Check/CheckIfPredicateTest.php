<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use PHPUnit\Framework\TestCase;
use Vivarium\Check\CheckIfPredicate;

/** @coversDefaultClass \Vivarium\Check\CheckIfPredicate */
final class CheckIfPredicateTest extends TestCase
{
    /** @covers ::isTrue() */
    public function testIsTrue(): void
    {
        static::assertTrue(CheckIfPredicate::isTrue(true));
        static::assertFalse(CheckIfPredicate::isTrue(false));
    }

    /** @covers ::isFalse() */
    public function testIsFalse(): void
    {
        static::assertTrue(CheckIfPredicate::isFalse(false));
        static::assertFalse(CheckIfPredicate::isFalse(true));
    }
}
