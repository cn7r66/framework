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
use Vivarium\Container\Installer\Installer;

final class BinderModuleTest extends TestCase
{
    public function testInstall(): void
    {
        $module = $this->createMock(BinderModule::class);
        $module->expects(static::once())
               ->method('configure')
               ->willReturnCallback(function (Binder $binder) {
                   return $binder->getInstaller();
               });

        $installer = new Installer();

        static::assertSame($installer, $module->install($installer));
    }
}