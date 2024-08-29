<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsIntersection;
use Vivarium\Test\Assertion\Stub\StubClass;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsIntersection */
final class IsIntersectionTest extends TestCase
{
    /**
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type): void
    {
        static::expectNotToPerformAssertions();

        (new IsIntersection())
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

        (new IsIntersection())
            ->assert($type);
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type): void
    {
        static::assertTrue(
            (new IsIntersection())($type),
        );
    }

    /**
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type): void
    {
        static::assertFalse(
            (new IsIntersection())($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['stdClass&Vivarium\Test\Assertion\Stub\StubClass'],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'NonExistentClass&stdClass',
                'Expected string to be intersection. Got "NonExistentClass&stdClass".',
            ],
            [
                StubClass::class,
                'Expected string to be intersection. Got "Vivarium\Test\Assertion\Stub\StubClass".',
            ],
            [
                'int&float',
                'Expected string to be intersection. Got "int&float".',
            ],
            [
                'stdClass&stdClass',
                'Expected string to be intersection. Got "stdClass&stdClass".',
            ],
            [
                'Vivarium\Test\Assertion\Stub\StubClass&',
                'Expected string to be intersection. Got "Vivarium\Test\Assertion\Stub\StubClass&".',
            ],
        ];
    }

    /** @return array<array{0:int, 1:string}> */
    public static function provideInvalid(): array
    {
        return [
            [42, 'Expected value to be string. Got integer'],
        ];
    }
}
