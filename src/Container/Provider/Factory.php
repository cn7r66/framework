<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Assertion\String\IsClass;
use Vivarium\Assertion\Type\IsCallable;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Factory implements Provider
{
    private Key $key;

    /** @var class-string */
    private string $class;

    private string $method;

    /** @param class-string $class */
    public function __construct(Key $key, string $class, string $method)
    {
        (new IsClass())
            ->assert($class);

        $this->key    = $key;
        $this->class  = $class;
        $this->method = $method;
    }

    public function provide(Container $container): mixed
    {
        $callable = [
            $container->get(new Key($this->class)),
            $this->method
        ];

        (new IsCallable())
            ->assert($callable);

        return call_user_func(
            $callable,
            $this->key
        );
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}