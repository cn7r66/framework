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

class Service implements Provider
{
    private Provider $provider;

    private $instance;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function provide(Container $container): mixed
    {
        if ($this->instance === null) {
            $this->instance = $this->provider->provide($container);
        }

        return $this->instance;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }
}