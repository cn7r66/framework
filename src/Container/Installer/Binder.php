<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

final class Binder
{
    private Installer $installer;

    public function __construct(Installer $installer)
    {
        $this->installer = $installer;
    }
}
