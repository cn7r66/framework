<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Solver;

use Psr\Log\LoggerInterface;
use Vivarium\Container\Binding;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver;

final class Logging implements Solver
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function solve(Binding $request, callable $next): Provider
    {
        // TODO Implements caching
        return $next();
    }
}
