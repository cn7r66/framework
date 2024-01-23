<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Provider;

final class Instance implements Provider
{
    public function __construct(private mixed $instance)
    {
    }

    public function provide(Container $container): mixed
    {
        return $this->instance;
    }
}
