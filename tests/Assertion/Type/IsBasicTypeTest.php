<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsBasicType;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsBasicType */
final class IsBasicTypeTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type): void
    {
        static::expectNotToPerformAssertions();

        (new IsBasicType())
            ->assert($type);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string|int $type, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsBasicType())
            ->assert($type);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type): void
    {
        static::assertTrue(
            (new IsBasicType())($type),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type): void
    {
        static::assertFalse(
            (new IsBasicType())($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['int'],
            ['float'],
            ['string'],
            ['callable'],
            ['object'],
            ['array'],
            ['stdClass'],
            ['Vivarium\Test\Assertion\Stub\StubClass'],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'stdClass|StubClass',
                'Expected string to be a primitive type, class or interface. Got "stdClass|StubClass".',
            ],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideInvalid(): array
    {
        return [
            [1, 'Expected string to be a primitive type, class or interface. Got 1.'],
        ];
    }
}
