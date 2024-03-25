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
use Vivarium\Container\Binding;

use function sprintf;

final class InaccesibleMethod extends RuntimeException
{
    public function __construct(private string $class, private string $method, int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct(
            sprintf(
                'Method %s, of class %s is not accessible.',
                $method,
                $class,
            ),
            $code,
            $previous,
        );
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
