<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class ConcreteStub extends BaseStub
{
    private int $n;

    public function __construct()
    {
        $this->n = 0;
    }

    public function setInt(int $n): int
    {
        $old = $this->n;

        $this->n = $n;

        return $old;
    }

    public function getInt(): int
    {
        return $this->n;
    }
}