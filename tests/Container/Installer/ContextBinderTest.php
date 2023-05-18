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
use Vivarium\Container\Installer\ContextBinder;
use Vivarium\Container\Installer\Installer;
use Vivarium\Container\Key;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Installer\ContextBinder */
final class ContextBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::for()
     */
    public function testFor(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage(
            'Expected string to be $GLOBAL, class, interface or namespace. Got "%namespace".',
        );

        $binder = new ContextBinder(
            new Installer(),
            new Key('int'),
        );

        $binder->for(Stub::class);
        $binder->for('%namespace');
    }
}
