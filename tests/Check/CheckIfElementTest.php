<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use Vivarium\Check\CheckIfElement;

/** @coversDefaultClass \Vivarium\Check\CheckIfElement */
final class CheckIfElementTest extends CheckTestCase
{
    public const NAMESPACE = 'Vivarium\Test\Assertion\Comparison';

    /**
     * @covers ::__callStatic()
     * @dataProvider provideMethods()
     */
    public function testCallStatic(string $method): void
    {
        $this->doTest(
            CheckIfElement::class,
            $method,
            self::NAMESPACE,
        );
    }

    /** @return array<array<string>> */
    public static function provideMethods(): array
    {
        return [
            ['isEqualsTo'],
            ['isOneOf'],
            ['isSameOf'],
        ];
    }
}
