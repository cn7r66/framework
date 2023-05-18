<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Installer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Installer\ConcreteBinder;
use Vivarium\Container\Installer\CoreSolvers;
use Vivarium\Container\Installer\Installer;
use Vivarium\Container\Key;
use Vivarium\Test\Assertion\Stub\StubClass;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubFactory;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Installer\ConcreteBinder */
final class ConcreteBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::to()
     */
    public function testTo(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected type "stdClass" to be assignable to "Vivarium\Test\Container\Stub\StubInterface".',
        );

        $binder = new ConcreteBinder(
            new Installer(),
            new Key(Stub::class),
        );

        $binder->to(StubImpl::class);
        $binder->to(stdClass::class);
    }

    /** @covers ::toInstance() */
    public function testToInstance(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected type "stdClass" to be assignable to "Vivarium\Test\Container\Stub\StubInterface".',
        );

        $installer = (new CoreSolvers())
            ->install(new Installer());

        $binder = new ConcreteBinder(
            $installer,
            new Key(Stub::class),
        );

        $binder->toInstance(new StubImpl());
        $binder->toInstance(new stdClass());
    }

    /** @covers ::toFactory() */
    public function testToFactory(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected "stdClass" to have a method named "create".');

        $binder = new ConcreteBinder(
            new Installer(),
            new Key(StubClass::class),
        );

        $binder->toFactory(StubFactory::class, 'create');
        $binder->toFactory(stdClass::class, 'create');
    }
}
