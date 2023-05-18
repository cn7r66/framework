<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Vivarium\Assertion\Type\IsObject;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Cloneable implements Provider
{
    private object|null $instance;

    public function __construct(private Provider $provider)
    {
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
