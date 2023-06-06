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

final class FactoryContextBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key,
        private Key $factory,
        private string $method,
    ) {
    }

    public function sameContext(): FactoryTagBinder
    {
        return new FactoryTagBinder(
            $this->installer,
            $this->key,
            new Key(
                $this->factory->getType(),
                $this->key->getContext(),
            ),
            $this->method,
        );
    }

    public function global(): FactoryTagBinder
    {
        return new FactoryTagBinder(
            $this->installer,
            $this->key,
            $this->factory,
            $this->method,
        );
    }
}
