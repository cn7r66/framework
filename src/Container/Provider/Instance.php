<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Instance implements Provider
{
    public function __construct(
        private Key $key,
        private mixed $instance,
    ) {
    }

    public function provide(Container $container): mixed
    {
        return $this->instance;
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}
