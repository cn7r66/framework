<?php

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Provider;

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

final class Fallback implements Provider
{
    public function __construct(private Provider $provider, private mixed $default)
    {
    }

    public function provide(Container $container): mixed
    {
        try {
            $this->provider->provide($container);
        } catch (Throwable) {
            return $this->default;
        }
    }
}
