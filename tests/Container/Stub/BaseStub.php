<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

abstract class BaseStub implements Stub
{
    public int $value = 0;

    public function do(): int
    {
        return $this->value === 0 ? 42 : $this->value;
    }
}
