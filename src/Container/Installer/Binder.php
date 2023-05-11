<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Assertion\String\IsType;
use Vivarium\Container\Key;

final class Binder
{
    private Installer $installer;

    public function __construct(Installer $installer)
    {
        $this->installer = $installer;
    }

    public function bind(string $type): ContextBinder
    {
        return new ContextBinder(
            $this->installer,
            new Key($type)
        );
    }

    public function getInstaller(): Installer
    {
        return $this->installer;
    }
}
