<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class DefaultArgumentStub
{
    public function __construct(private string $stub = 'DEFAULT')
    {
    }

    public function getString(): string
    {
        return $this->stub;
    }
}
