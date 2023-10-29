<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Key;
use Vivarium\Equality\Equal;
use Vivarium\Test\Container\Stub\Foo;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

use function var_dump;

/** @coversDefaultClass \Vivarium\Container\Key */
final class KeyTest extends TestCase
{
    /** @covers ::__construct() */
    public function testConstructorExceptionOnType(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be a primitive, class, interface, union or intersection. Got "random-string".',
        );

        new Key(Stub::class);
        new Key('int');
        new Key('array|stdClass');

        new Key('random-string');
    }

    /** @covers ::__construct() */
    public function testConstructorExceptionOnContext(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be $GLOBAL, class, interface or namespace. Got "random-string".',
        );

        new Key(Stub::class);
        new Key('int', Stub::class);
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
        $key = new Key(Stub::class);

        static::assertSame(Stub::class, $key->getType());
        static::assertSame(KEY::GLOBAL, $key->getContext());
        static::assertSame(Key::DEFAULT, $key->getTag());
    }

    /**
     * @covers ::equals()
     * @covers ::hash()
     */
    public function testEquality(): void
    {
        $first  = new Key('int', Stub::class, 'tag');
        $second = new Key('int', Stub::class, 'tag');

        static::assertFalse($first->equals(new stdClass()));
        static::assertTrue($first->equals($first));
        static::assertTrue($first->equals($second));
        static::assertTrue($second->equals($first));
        static::assertSame($first->hash(), $second->hash());
    }

    /**
     * @covers ::couldBeWidened()
     * @covers ::widen()
     */
    public function testWiden(): void
    {
        $key = new Key(
            StubImpl::class,
            Foo::class,
            'Stub.Impl',
        );

        $key = $key->widen();
        static::assertTrue(
            Equal::areEquals(
                new Key(
                    StubImpl::class,
                    Foo::class,
                ),
                $key,
            ),
        );

        $expected = [
            'Vivarium\Test\Container\Stub',
            'Vivarium\Test\Container',
            'Vivarium\Test',
            'Vivarium',
            Key::GLOBAL,
        ];

        foreach ($expected as $current) {
            static::assertTrue($key->couldBeWidened());

            $key = $key->widen();

            static::assertTrue(
                Equal::areEquals(
                    new Key(
                        StubImpl::class,
                        $current,
                    ),
                    $key,
                ),
            );
        }

        static::assertFalse($key->couldBeWidened());
    }
}
