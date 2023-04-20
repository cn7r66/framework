<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Assertion\Type\IsObject;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Cloneable implements Provider
{
    private Provider $provider;

    private null|object $instance;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
        $this->instance = null;
    }

    public function provide(Container $container): mixed
    {
        if ($this->instance === null) {
            $instance = $this->provider->provide($container);

            (new IsObject())
                ->assert($instance);

            $this->instance = $instance;
        }

        return clone $this->instance;
    }

    public function getKey(): Key
    {
        return $this->provider->getKey();
    }
}