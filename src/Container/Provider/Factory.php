<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Factory implements Provider
{
    /** @var class-string */
    private string $class;

    private Key $key;

    /** @param class-string $class */
    public function __construct(string $class, Key $key)
    {
        $this->class = $class;
        $this->key   = $key;
    }

    public function provide(Container $container): mixed
    {
        $factory = $container->get(new Key($this->class));

        return $factory->create($this->key);
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}