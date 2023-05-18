<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Key;
use Vivarium\Container\Solver\Registry;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Solver\Registry */
final class RegistryTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::hasExactly()
     * @covers ::add()
     */
    public function testHasExactly(): void
    {
        $key1 = new Key('int');
        $key2 = new Key('int', Stub::class);
        $key3 = new Key('int', Stub::class, 'test.int');
        $key4 = new Key('int', 'Vivarium\\Test\\Stub');

        $registry = (new Registry())
            ->add($key1, 1)
            ->add($key2, 2)
            ->add($key3, 3);

        static::assertTrue($registry->hasExactly($key1));
        static::assertTrue($registry->hasExactly($key2));
        static::assertTrue($registry->hasExactly($key3));
        static::assertFalse($registry->hasExactly($key4));
    }

    /**
     * @covers ::__construct()
     * @covers ::getExactly()
     * @covers ::add()
     */
    public function testGetExactly(): void
    {
        $key1 = new Key('int');
        $key2 = new Key('int', Stub::class);
        $key3 = new Key('int', Stub::class, 'test.int');

        $registry = (new Registry())
            ->add($key1, 1)
            ->add($key2, 2)
            ->add($key3, 3);

        static::assertSame(1, $registry->getExactly($key1));
        static::assertSame(2, $registry->getExactly($key2));
        static::assertSame(3, $registry->getExactly($key3));
    }

    /** @covers ::getExactly() */
    public function testGetExactlyException(): void
    {
        static::expectException(OutOfBoundsException::class);
        static::expectExceptionMessage('The provided key is not present.');

        (new Registry())
            ->get(new Key('int'));
    }

    /**
     * @covers ::has()
     * @covers ::widen()
     */
    public function testHas(): void
    {
        $key1 = new Key('int', Stub::class);
        $key2 = new Key('int', Stub::class, 'test.int');
        $key3 = new Key('int');
        $key4 = new Key('int', 'Vivarium\Test\Stub');
        $key5 = new Key('int', StubImpl::class);
        $key6 = new Key('int', stdClass::class);
        $key7 = new Key('float', Stub::class);

        $registry = (new Registry())
            ->add($key1, 1)
            ->add($key2, 2)
            ->add($key3, 3)
            ->add($key4, 4);

        static::assertTrue($registry->has($key1));
        static::assertTrue($registry->has($key2));
        static::assertTrue($registry->has($key3));
        static::assertTrue($registry->has($key5));
        static::assertTrue($registry->has($key6));
        static::assertFalse($registry->has($key7));
    }

    /**
     * @covers ::get()
     * @covers ::widen()
     */
    public function testGet(): void
    {
        $key1 = new Key('int', Stub::class);
        $key2 = new Key('int', Stub::class, 'test.int');
        $key3 = new Key('int');
        $key4 = new Key('int', 'Vivarium\Test\Container\Stub');
        $key5 = new Key('int', StubImpl::class);
        $key6 = new Key('int', stdClass::class);

        $registry = (new Registry())
            ->add($key1, 1)
            ->add($key2, 2)
            ->add($key3, 3)
            ->add($key4, 4);

        static::assertSame(1, $registry->get($key1));
        static::assertSame(2, $registry->get($key2));
        static::assertSame(3, $registry->get($key3));
        static::assertSame(4, $registry->get($key5));
        static::assertSame(3, $registry->get($key6));
    }
}
