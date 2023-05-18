<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;

final class Binder
{
    public function __construct(private Installer $installer)
    {
    }

    /** @param non-empty-string|class-string $type */
    public function bind(string $type): ContextBinder
    {
        return new ContextBinder(
            $this->installer,
            new Key($type),
        );
    }

    public function getInstaller(): Installer
    {
        return $this->installer;
    }
}
