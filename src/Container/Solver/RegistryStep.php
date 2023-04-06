<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Solver;

use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class RegistryStep implements SolverStep
{
    /** @var Registry<callable(Key): Provider> */
    private Registry $registry;

    public function __construct()
    {
        $this->registry = new Registry();
    }

    public function request(Key $key): Response
    {
        if (! $this->registry->has($key)) {
            return Response::notSolved();
        }

        return Response::solved(
            $this->registry->get($key)($key)
        );
    }

    public function response(Key $key, Response $response): Response
    {
        return $response;
    }

    public function addSolver(Key $key, callable $solver): self
    {
        $registry = clone $this;
        $registry->registry = $registry->registry->add($key, $solver);

        return $registry;
    }
}
