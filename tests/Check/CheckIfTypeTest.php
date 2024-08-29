<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

namespace Vivarium\Test\Check;

use Vivarium\Check\CheckIfType;

/** @coversDefaultClass \Vivarium\Check\CheckIfType */
final class CheckIfTypeTest extends CheckTestCase
{
    public const NAMESPACE = 'Vivarium\Test\Assertion\Type';

    /**
     * @covers ::__callStatic()
     * @dataProvider provideMethods()
     */
    public function testCallStatic(string $method): void
    {
        $this->doTest(
            CheckIfType::class,
            $method,
            self::NAMESPACE,
        );
    }

    /** @return array<array<string>> */
    public static function provideMethods(): array
    {
        return [
            ['ImplementsInterface'],
            ['IsAssignableTo'],
            ['IsAssignableToClass'],
            ['IsAssignableToIntersection'],
            ['IsAssignableToPrimitive'],
            ['IsAssignableToUnion'],
            ['IsSubclassOf'],
            ['IsBasicType'],
            ['IsClass'],
            ['IsClassOrInterface'],
            ['IsInterface'],
            ['IsIntersection'],
            ['IsNamespace'],
            ['IsPrimitive'],
            ['IsType'],
            ['IsUnion'],
        ];
    }
}
