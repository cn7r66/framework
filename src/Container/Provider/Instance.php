<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Provider;

final class Instance implements Provider
{
    private $instance;

    public function __construct($instance)
    {
        $this->instance = $instance;
    }

    public function provide(Container $container)
    {
        return $this->instance;
    }
}