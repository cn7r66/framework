<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsType;

use function array_merge;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsType */
final class IsTypeTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type): void
    {
        static::expectNotToPerformAssertions();

        (new IsType())
            ->assert($type);
    }

    /**
     * @covers ::assert()
     * @dataProvider provideFailure()
     */
    public function testAssertException(string $type, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsType())
            ->assert($type);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type): void
    {
        static::assertTrue(
            (new IsType())($type),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type): void
    {
        static::assertFalse(
            (new IsType())($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return array_merge(
            IsBasicTypeTest::provideSuccess(),
            IsClassOrInterfaceTest::provideSuccess(),
            IsClassTest::provideSuccess(),
            IsInterfaceTest::provideSuccess(),
            IsIntersectionTest::provideSuccess(),
            IsPrimitiveTest::provideSuccess(),
            IsUnionTest::provideSuccess(),
        );
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'NonExistent',
                'Expected string to be a primitive, class, interface, union or intersection. Got "NonExistent".',
            ],
        ];
    }
}
