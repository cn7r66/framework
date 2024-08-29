<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsAssignableToUnion;
use Vivarium\Test\Assertion\Stub\StubClass;

use function sprintf;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsAssignableToUnion */
final class IsAssignableToUnionTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type, string $union): void
    {
        static::expectNotToPerformAssertions();

        (new IsAssignableToUnion($union))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string $type, string $union, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsAssignableToUnion($union))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type, string $union): void
    {
        static::assertTrue(
            (new IsAssignableToUnion($union))($type),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type, string $union): void
    {
        static::assertFalse(
            (new IsAssignableToUnion($union))($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            [
                stdClass::class,
                stdClass::class . '|' . StubClass::class,
            ],
            [
                StubClass::class,
                stdClass::class . '|' . StubClass::class,
            ],
            [
                'int',
                'int|float',
            ],
            [
                'float',
                'int|float',
            ],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'float',
                stdClass::class . '|' . StubClass::class,
                sprintf(
                    'Expected type to be assignable to union "%s" Got "float".',
                    stdClass::class . '|' . StubClass::class,
                ),
            ],
            [
                StubClass::class,
                'int|float',
                sprintf(
                    'Expected type to be assignable to union "int|float" Got "%s".',
                    StubClass::class,
                ),
            ],
        ];
    }

    /** @return array<array<string>> */
    public static function provideInvalid(): array
    {
        return [
            [
                stdClass::class,
                'Foo&Bar',
                'Expected string to be union. Got "Foo&Bar".',
            ],
        ];
    }
}
