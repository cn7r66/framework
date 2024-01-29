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

final class CannotBeWidened extends RuntimeException
{
    public function __construct(private Binding $binding, int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct(
            sprintf(
                'Binding with id %s, context %s and tag %s cannot be widened.',
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
