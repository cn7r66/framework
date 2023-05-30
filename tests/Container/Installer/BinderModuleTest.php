<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Installer;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Installer\Binder;
use Vivarium\Container\Installer\BinderModule;
use Vivarium\Container\Installer\ConfigurableInstaller;
use Vivarium\Container\Installer\Installer;

/** @coversDefaultClass \Vivarium\Container\Installer\BinderModule */
final class BinderModuleTest extends TestCase
{
    /** @covers ::install() */
    public function testInstall(): void
    {
        $installer = static::createMock(Installer::class);

        $module = $this->getMockBuilder(BinderModule::class)
            ->onlyMethods(['configure'])
            ->getMock();

        $module->expects(static::once())
               ->method('configure')
               ->willReturnCallback(function (Binder $binder) {
                   return $binder->getInstaller();
               });

        static::assertSame($installer, $module->install($installer));
    }
}