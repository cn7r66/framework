<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Installer\Binder\Binder;

abstract class FluentModule implements Module
{
    public function install(Installer $installer): Installer
    {
        return $this->configure(
            new Binder($installer),
        );
    }

    abstract protected function configure(Binder $binder): Installer;
}
