<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsAssignableToPrimitive;
use Vivarium\Test\Assertion\Stub\StubClass;
use Vivarium\Test\Assertion\Stub\StubClassExtension;

use function sprintf;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsAssignableToPrimitive */
final class IsAssignableToPrimitiveTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $type, string $primitive): void
    {
        static::expectNotToPerformAssertions();

        (new IsAssignableToPrimitive($primitive))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string $type, string $primitive, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsAssignableToPrimitive($primitive))
            ->assert($type);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $type, string $primitive): void
    {
        static::assertTrue(
            (new IsAssignableToPrimitive($primitive))($type),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $type, string $primitive): void
    {
        static::assertFalse(
            (new IsAssignableToPrimitive($primitive))($type),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            ['int', 'float'],
            ['string', 'string'],
            [StubClass::class, 'string'],
            [StubClass::class, 'object'],
            [StubClassExtension::class, 'callable'],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                'float',
                'int',
                'Expected type to be assignable to primitive type "int". Got "float".',
            ],
            [
                StubClass::class,
                'int',
                sprintf(
                    'Expected type to be assignable to primitive type "int". Got "%s".',
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
                'RandomString',
                'Expected string to be a primitive type. Got "RandomString".',
            ],
        ];
    }
}
