<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Container\Solver\CachedStep;
use Vivarium\Container\Solver\DecoratorStep;
use Vivarium\Container\Solver\DirectStep;
use Vivarium\Container\Solver\InjectorStep;
use Vivarium\Container\Solver\LoggedStep;
use Vivarium\Container\Solver\PrototypeStep;
use Vivarium\Container\Solver\ScopeStep;

final class CoreSolvers implements Module
{
    public function install(Installer $installer): Installer
    {
        return $installer
            ->withStepFactory(LoggedStep::class, static function () {
                return new LoggedStep();
            }, 10)

            ->withStepFactory(CachedStep::class, static function () {
                return new CachedStep();
            }, 20)

            ->withStepFactory(ScopeStep::class, static function () {
                return new ScopeStep();
            }, 30)

            ->withStepFactory(DecoratorStep::class, static function () {
                return new DecoratorStep();
            }, 40)

            ->withStepFactory(InjectorStep::class, static function () {
                return new InjectorStep();
            }, 50)

            ->withStepFactory(DirectStep::class, static function () {
                return new DirectStep();
            }, 60)

            ->withStepFactory(PrototypeStep::class, static function () {
                return new PrototypeStep();
            }, 70);
    }
}
