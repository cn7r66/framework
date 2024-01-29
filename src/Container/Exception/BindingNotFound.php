<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Throwable;
use Vivarium\Container\Binding;

use function sprintf;

final class BindingNotFound extends RuntimeException implements NotFoundExceptionInterface
{
    public function __construct(private Binding $binding, int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct(
            sprintf(
                'Binding with id %s, context %s and tag %s not found.',
                $binding->getId(),
                $binding->getContext(),
                $binding->getTag(),
            ),
            $code,
            $previous,
        );
    }

    public function getBinding(): Binding
    {
        return $this->binding;
    }
}
