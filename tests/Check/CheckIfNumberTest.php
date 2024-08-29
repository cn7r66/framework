<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use Vivarium\Check\CheckIfNumber;

/** @coversDefaultClass \Vivarium\Check\CheckIfNumber */
final class CheckIfNumberTest extends CheckTestCase
{
    public const NAMESPACE = 'Vivarium\Test\Assertion\Numeric';

    /**
     * @covers ::__callStatic()
     * @dataProvider provideMethods()
     */
    public function testCallStatic(string $method): void
    {
        $this->doTest(
            CheckIfNumber::class,
            $method,
            self::NAMESPACE,
        );
    }

    /** @return array<array<string>> */
    public static function provideMethods(): array
    {
        return [
            ['isGreaterOrEqualThan'],
            ['isGreaterThan'],
            ['isInClosedRange'],
            ['isInHalfOpenLeftRange'],
            ['isInHalfOpenRightRange'],
            ['isInOpenRange'],
            ['isLessOrEqualThan'],
            ['isLessThan'],
            ['isOutOfClosedRange'],
            ['isOutOfOpenRange'],
        ];
    }
}
