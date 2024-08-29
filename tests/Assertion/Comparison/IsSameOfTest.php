<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Comparison;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Comparison\IsSameOf;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Test\Equality\Stub\EqualityStub;

/** @coversDefaultClass \Vivarium\Assertion\Comparison\IsSameOf */
final class IsSameOfTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testAssert(mixed $first, mixed $second): void
    {
        static::expectNotToPerformAssertions();

        (new IsSameOf($first))
            ->assert($second);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testAssertException(mixed $first, mixed $second, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsSameOf($first))
            ->assert($second);
    }

    /** @return array<array<scalar|object>> */
    public static function provideSuccess(): array
    {
        $stdClass = new stdClass();
        $equality = new EqualityStub();

        return [
            ['Foo', 'Foo'],
            [5, 5],
            [-1, -1],
            [$stdClass, $stdClass],
            [$equality, $equality],
        ];
    }

    /** @return array<array{0:string|object, 1:string|object, 2:string}> */
    public static function provideFailure(): array
    {
        return [
            [
                new stdClass(),
                new stdClass(),
                'Expected value to be the same of "stdClass". Got different object.',
            ],
            [
                'RandomString',
                'Hello World',
                'Expected value to be the same of "RandomString". Got "Hello World".',
            ],
            [
                new EqualityStub(),
                new EqualityStub(),
                'Expected value to be the same of "Vivarium\Test\Equality\Stub\EqualityStub". Got different object.',
            ],
        ];
    }
}
