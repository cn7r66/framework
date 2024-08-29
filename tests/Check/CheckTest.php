<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Check;

use Vivarium\Check\Check;
use Vivarium\Check\Exception\NoSuchMethod;
use Vivarium\Check\Exception\TooFewArguments;
use Vivarium\Check\Exception\TooMuchArguments;

/** @coversDefaultClass \Vivarium\Check\Check */
final class CheckTest extends CheckTestCase
{
    public const COMPARISON_NAMESPACE = 'Vivarium\Test\Assertion\Comparison';
    public const ENCODING_NAMESPACE   = 'Vivarium\Test\Assertion\Encoding';
    public const NUMERIC_NAMESPACE    = 'Vivarium\Test\Assertion\Numeric';
    public const OBJECT_NAMESPACE     = 'Vivarium\Test\Assertion\Object';
    public const STRING_NAMESPACE     = 'Vivarium\Test\Assertion\String';
    public const TYPE_NAMESPACE       = 'Vivarium\Test\Assertion\Type';
    public const VAR_NAMESPACE        = 'Vivarium\Test\Assertion\Var';

    /**
     * @covers ::__construct()
     * @covers ::boolean()
     * @covers ::__call()
     */
    public function testBoolean(): void
    {
        $check = Check::boolean();

        // @phpstan-ignore method.notFound
        static::assertTrue($check->isTrue(true));

        // @phpstan-ignore method.notFound
        static::assertTrue($check->isFalse(false));

        // @phpstan-ignore method.notFound
        static::assertFalse($check->isTrue(false));

        // @phpstan-ignore method.notFound
        static::assertFalse($check->isFalse(true));
    }

    /**
     * @covers ::__construct()
     * @covers ::comparison()
     * @covers ::__call()
     * @dataProvider \Vivarium\Test\Check\CheckIfElementTest::provideMethods()
     */
    public function testComparison(string $method): void
    {
        $this->doTest(
            Check::comparison(),
            $method,
            self::COMPARISON_NAMESPACE,
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::encoding()
     * @covers ::__call()
     * @dataProvider \Vivarium\Test\Check\CheckIfEncodingTest::provideMethods()
     */
    public function testEncoding(string $method): void
    {
        $this->doTest(
            Check::encoding(),
            $method,
            self::ENCODING_NAMESPACE,
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::numeric()
     * @covers ::__call()
     * @dataProvider \Vivarium\Test\Check\CheckIfNumberTest::provideMethods()
     */
    public function testNumeric(string $method): void
    {
        $this->doTest(
            Check::numeric(),
            $method,
            self::NUMERIC_NAMESPACE,
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::object()
     * @covers ::__call()
     * @dataProvider \Vivarium\Test\Check\CheckIfObjectTest::provideMethods()
     */
    public function testObject(string $method): void
    {
        $this->doTest(
            Check::object(),
            $method,
            self::OBJECT_NAMESPACE,
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::string()
     * @covers ::__call()
     * @dataProvider \Vivarium\Test\Check\CheckIfStringTest::provideMethods()
     */
    public function testString(string $method): void
    {
        $this->doTest(
            Check::string(),
            $method,
            self::STRING_NAMESPACE,
        );
    }

        /**
         * @covers ::__construct()
         * @covers ::type()
         * @covers ::__call()
         * @dataProvider \Vivarium\Test\Check\CheckIfTypeTest::provideMethods()
         */
    public function testType(string $method): void
    {
        $this->doTest(
            Check::type(),
            $method,
            self::TYPE_NAMESPACE,
        );
    }

        /**
         * @covers ::__construct()
         * @covers ::var()
         * @covers ::__call()
         * @dataProvider \Vivarium\Test\Check\CheckIfVarTest::provideMethods()
         */
    public function testVar(string $method): void
    {
        $this->doTest(
            Check::var(),
            $method,
            self::VAR_NAMESPACE,
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__call()
     */
    public function testNoSuchMethod(): void
    {
        static::expectException(NoSuchMethod::class);
        static::expectExceptionMessage(
            'No such method nonExistentMethod. Missing class Vivarium\Assertion\String\NonExistentMethod',
        );

        $check = Check::string();

        // @phpstan-ignore method.notFound
        $check->nonExistentMethod();
    }

    /**
     * @covers ::__construct()
     * @covers ::__call()
     */
    public function testTooFewArguments(): void
    {
        static::expectException(TooFewArguments::class);
        static::expectExceptionMessage(
            'Too few arguments provided. Expected 1, got 0',
        );

        $check = Check::boolean();

        // @phpstan-ignore method.notFound
        $check->isTrue();
    }

    /**
     * @covers ::__construct()
     * @covers ::__call()
     */
    public function testTooMuchArguments(): void
    {
        static::expectException(TooMuchArguments::class);
        static::expectExceptionMessage(
            'Too much arguments provided. Expected 1, got 2',
        );

        $check = Check::boolean();

        // @phpstan-ignore method.notFound
        $check->isTrue(true, false);
    }
}
