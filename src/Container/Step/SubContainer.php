<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Step;

use Psr\Container\ContainerInterface;
use Vivarium\Container\Binding;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Step;

final class SubContainer implements Step
{
    public function __construct(private ContainerInterface $container)
    {
    }

    /** @param callable(): Provider $next */
    public function solve(Binding $request, callable $next): Provider
    {
        if (! $this->container->has($request->getId())) {
            return $next();
        }

        return new Instance(
            $this->container->get($request->getId()),
        );
    }
}
