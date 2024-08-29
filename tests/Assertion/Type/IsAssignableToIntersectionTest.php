<?php

declare(strict_types=1);

namespace Vivarium\Test\Assertion\Type;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Type\IsAssignableToIntersection;
use Vivarium\Test\Assertion\Stub\InvokableStub;
use Vivarium\Test\Assertion\Stub\Stub;
use Vivarium\Test\Assertion\Stub\StubClass;
use Vivarium\Test\Assertion\Stub\StubClassExtension;

use function sprintf;

/** @coversDefaultClass \Vivarium\Assertion\Type\IsAssignableToIntersection */
final class IsAssignableToIntersectionTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideSuccess()
     */
    public function testAssert(string $class, string $intersection): void
    {
        static::expectNotToPerformAssertions();

        (new IsAssignableToIntersection($intersection))
            ->assert($class);
    }

    /**
     * @covers ::__construct()
     * @covers ::assert()
     * @dataProvider provideFailure()
     * @dataProvider provideInvalid()
     */
    public function testAssertException(string $class, string $intersection, string $message): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage($message);

        (new IsAssignableToIntersection($intersection))
            ->assert($class);
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideSuccess()
     */
    public function testInvoke(string $class, string $intersection): void
    {
        static::assertTrue(
            (new IsAssignableToIntersection($intersection))($class),
        );
    }

    /**
     * @covers ::__construct()
     * @covers ::__invoke()
     * @dataProvider provideFailure()
     */
    public function testInvokeFailure(string $class, string $intersection): void
    {
        static::assertFalse(
            (new IsAssignableToIntersection($intersection))($class),
        );
    }

    /** @return array<array<string>> */
    public static function provideSuccess(): array
    {
        return [
            [
                StubClassExtension::class,
                Stub::class . '&' . InvokableStub::class,
            ],
        ];
    }

    /** @return array<array<string>> */
    public static function provideFailure(): array
    {
        return [
            [
                StubClass::class,
                Stub::class . '&' . InvokableStub::class,
                sprintf(
                    'Expected class to be assignable to intersection "%s" Got "%s".',
                    Stub::class . '&' . InvokableStub::class,
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
                'int&string',
                'Expected string to be intersection. Got "int&string".',
            ],
        ];
    }
}
