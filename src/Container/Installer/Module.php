<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

interface Module
{
    public function install(Installer $installer): Installer;
}
