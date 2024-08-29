<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use Vivarium\Check\CheckIfEncoding;

/** @coversDefaultClass \Vivarium\Check\CheckIfEncoding */
final class CheckIfEncodingTest extends CheckTestCase
{
    public const NAMESPACE = 'Vivarium\Test\Assertion\Encoding';

    /**
     * @covers ::__callStatic()
     * @dataProvider provideMethods()
     */
    public function testCallStatic(string $method): void
    {
        $this->doTest(
            CheckIfEncoding::class,
            $method,
            self::NAMESPACE,
        );
    }

    /** @return array<array<string>> */
    public static function provideMethods(): array
    {
        return [
            ['isEncoding'],
            ['isRegexEncoding'],
            ['isSystemEncoding'],
        ];
    }
}
