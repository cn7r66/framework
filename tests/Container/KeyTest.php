<?php declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Key;
use Vivarium\Test\Container\Stub\StubInterface;

/** @coversDefaultClass \Vivarium\Container\Key */
final class KeyTest extends TestCase
{
    /** @covers ::__construct() */
    public function testConstructorExceptionOnType(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be a primitive, class, interface, union or intersection. Got "random-string".'
        );

        new Key(StubInterface::class);
        new Key('int');
        new Key('array|stdClass');

        new Key('random-string');
    }

    /** @covers ::__construct() */
    public function testConstructorExceptionOnContext(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be $GLOBAL, class, interface or namespace. Got "random-string".'
        );

        new Key(StubInterface::class);
        new Key('int', StubInterface::class);
        new Key('array|stdClass', 'Vivarium\Test\Container');

        new Key('array', 'random-string');
    }

    /**
     * @covers ::getType()
     * @covers ::getContext()
     * @covers ::getTag()
     */
    public function testGetters(): void
    {
        $key = new Key(StubInterface::class);

        static::assertSame(StubInterface::class, $key->getType());
        static::assertSame(KEY::GLOBAL, $key->getContext());
        static::assertSame(Key::DEFAULT, $key->getTag());
    }

    /**
     * @covers ::equals()
     * @covers ::hash()
     */
    public function testEquality(): void
    {
        $first  = new Key('int', StubInterface::class, 'tag');
        $second = new Key('int', StubInterface::class, 'tag');

        static::assertFalse($first->equals(new stdClass()));
        static::assertTrue($first->equals($first));
        static::assertTrue($first->equals($second));
        static::assertTrue($second->equals($first));
        static::assertSame($first->hash(), $second->hash());
    }
}