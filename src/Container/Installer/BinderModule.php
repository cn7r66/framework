<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

abstract class BinderModule implements Module
{
    public function install(Installer $installer): Installer
    {
        return $this->configure(
            new Binder($installer)
        );
    }

    protected abstract function configure(Binder $binder): Installer;
}
