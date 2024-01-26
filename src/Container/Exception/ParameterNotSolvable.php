<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Exception;

use RuntimeException;
use Throwable;

final class ParameterNotSolvable extends RuntimeException
{
    public function __construct(private string $parameter, private string $method, int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct(
            sprintf(
                "Parameter named %s in method %s is not solvable.",
                $parameter,
                $method
            ),
            $code,
            $previous
        );
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
