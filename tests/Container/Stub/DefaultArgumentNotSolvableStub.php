<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class DefaultArgumentNotSolvableStub
{
    public function __construct(private $value = 'DEFAULT') // phpcs:ignore
    {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}
