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

use function sprintf;

final class PropertyNotFound extends RuntimeException
{
    public function __construct(
        private string $class,
        private string $property,
        int $code = 0,
        Throwable|null $previous = null,
    ) {
        parent::__construct(
            sprintf(
                'Property named %s in class %s not found.',
                $property,
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

    public function getProperty(): string
    {
        return $this->property;
    }
}
