<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Instance implements Provider
{
    private mixed $instance;

    public function __construct( mixed $instance)
    {
        $this->instance = $instance;
    }

    public function provide(Container $container, ?string $requester = null): mixed
    {
        return $this->instance;
    }
}
