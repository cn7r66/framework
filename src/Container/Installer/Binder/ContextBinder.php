<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer\Binder;

use Vivarium\Container\Installer\Installer;
use Vivarium\Container\Key;

final class ContextBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key,
    ) {
    }

    public function global(): TagBinder
    {
        return new TagBinder(
            $this->installer,
            $this->key,
        );
    }

    /** @param non-empty-string|class-string $class */
    public function for(string $class): TagBinder
    {
        return new TagBinder(
            $this->installer,
            new Key(
                $this->key->getType(),
                $class,
            ),
        );
    }
}
