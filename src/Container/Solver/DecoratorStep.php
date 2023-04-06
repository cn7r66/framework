<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Vivarium\Container\Key;

final class DecoratorStep implements SolverStep
{
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function request(Key $key): Response
    {
        // TODO: Implement request() method.
    }

    public function response(Key $key, Response $response): Response
    {
        // TODO: Implement response() method.
    }
}
