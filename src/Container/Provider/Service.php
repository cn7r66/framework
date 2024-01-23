<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Service implements Provider
{
    private mixed $instance;

    public function __construct(private Provider $provider)
    {
        $this->instance = null;
    }

    public function provide(Container $container): mixed
    {
        if ($this->instance === null) {
            $this->instance = $this->provider->provide($container);
        }

        return $this->instance;
    }
}
