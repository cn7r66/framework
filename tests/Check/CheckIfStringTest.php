<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use Vivarium\Check\CheckIfString;

/** @coversDefaultClass \Vivarium\Check\CheckIfString */
final class CheckIfStringTest extends CheckTestCase
{
    public const NAMESPACE = 'Vivarium\Test\Assertion\String';

    /**
     * @covers ::__callStatic()
     * @dataProvider provideMethods()
     */
    public function testCallStatic(string $method): void
    {
        $this->doTest(
            CheckIfString::class,
            $method,
            self::NAMESPACE,
        );
    }

    /** @return array<array<string>> */
    public static function provideMethods(): array
    {
        return [
            ['contains'],
            ['endsWith'],
            ['isEmpty'],
            ['isLong'],
            ['isLongAtLeast'],
            ['isLongAtMax'],
            ['isLongBetween'],
            ['isNotEmpty'],
            ['startsWith'],
        ];
    }
}
