<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Installer;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Installer\Module;
use Vivarium\Container\Installer\ModuleLoader;

/** @coversDefaultClass \Vivarium\Container\Installer\ModuleLoader */
final class ModuleLoaderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::install()
     * @covers ::getSolver()
     */
    public function testGetSolver(): void
    {
        $module = static::createMock(Module::class);
        $module->expects(static::once())
               ->method('install')
               ->willReturnArgument(0);

        $loader1 = new ModuleLoader();
        $loader2 = $loader1->install($module);

        $loader2->getSolver();

        static::assertNotSame($loader1, $loader2);
    }
}