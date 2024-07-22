<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\String;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsUnion;
use Vivarium\Test\Assertion\Stub\InvokableStub;
use Vivarium\Test\Assertion\Stub\StubClass;
use Vivarium\Test\Assertion\Stub\StubClassExtension;

/** @coversDefaultClass \Vivarium\Assertion\String\IsUnion */
final class IsUnionTest extends TestCase
{
    /** @covers ::assert() */
    public function testAssert(): void
    {
        static::expectNotToPerformAssertions();

        (new IsUnion())
            ->assert('stdClass|Vivarium\Test\Assertion\Stub\StubClass');
    }

    /** @covers ::assert() */
    public function testAssertException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be union. Got "Foo|Bar".');

        (new IsUnion())
            ->assert('Foo|Bar');
    }

    /** @covers ::assert() */
    public function testAssertExceptionSingleString(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be union. Got "stdClass".');

        (new IsUnion())
            ->assert('stdClass');
    }

    /** @covers ::assert() */
    public function testAssertWithNonString(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected value to be string. Got integer.');

        (new IsUnion())
            ->assert(1);
    }

    /** @covers ::__invoke() */
    public function testInvoke(): void
    {
        static::assertTrue(
            (new IsUnion())('stdClass|Vivarium\Test\Assertion\Stub\StubClass'),
        );

        static::assertFalse(
            (new IsUnion())('Foo|Bar'),
        );

        static::assertFalse(
            (new IsUnion())('stdClass'),
        );

        static::assertTrue(
            (new IsUnion())(
                'Vivarium\Test\Assertion\Stub\StubClassExtension|
                (Vivarium\Test\Assertion\Stub\InvokableStub&Vivarium\Test\Assertion\Stub\StubClass)|
                stdClass'
            )
        );
    }
}
