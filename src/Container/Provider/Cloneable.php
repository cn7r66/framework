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

final class Cloneable implements Provider
{
    private Provider $provider;

    private mixed $instance;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->instance = null;
    }

    public function provide(Container $container, ?string $requester = null): mixed
    {
        if ($this->instance === null) {
            $this->instance = $this->provider->provide($container, $requester);
        }

        return clone $this->instance;
    }
}
