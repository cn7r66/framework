<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Container\Step\ConfigurableSolver;

interface Module
{
    public function install(ConfigurableSolver $solver): ConfigurableSolver;
}
