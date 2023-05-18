<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

abstract class BinderModule implements Module
{
    public function install(Installer $installer): Installer
    {
        return $this->configure(
            new Binder($installer),
        );
    }

    abstract protected function configure(Binder $binder): Installer;
}
