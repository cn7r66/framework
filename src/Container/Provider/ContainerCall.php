<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Binding;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;

final class ContainerCall implements Provider
{
    public function __construct(private Binding $target)
    {
    }

    public function provide(Container $container): mixed
    {
        return $container->get($this->target);
    }
}
