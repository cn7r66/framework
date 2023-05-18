<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Vivarium\Assertion\Type\IsCallable;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

use function call_user_func;

final class Factory implements Provider
{
    public function __construct(
        private Key $key,
        private Key $factory,
        private string $method,
    ) {
    }

    public function provide(Container $container): mixed
    {
        $callable = [
            $container->get($this->factory),
            $this->method,
        ];

        (new IsCallable())
            ->assert($callable);

        return call_user_func(
            $callable,
            $this->key,
        );
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}
