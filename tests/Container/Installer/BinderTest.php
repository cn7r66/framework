<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Installer;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Installer\Binder;
use Vivarium\Container\Installer\ConfigurableInstaller;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Installer\Binder */
final class BinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::bind
     */
    public function testBinderException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be a primitive, class, interface, union or intersection. Got "random-string".',
        );

        $binder = new Binder(new ConfigurableInstaller());

        $binder->bind('int');
        $binder->bind('array|stdClass');
        $binder->bind(Stub::class);
        $binder->bind('random-string');
    }

    /** @covers ::getInstaller */
    public function testBinderImmutability(): void
    {
        $installer = new ConfigurableInstaller();

        $binder = new Binder($installer);

        $binder->bind(Stub::class);
        $binder->bind('int');

        static::assertSame($installer, $binder->getInstaller());
    }
}
