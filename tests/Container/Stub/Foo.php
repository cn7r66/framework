<?php declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Stub;

final class Foo
{
    public function __construct(
        private Stub $stub
    ) {}

    public function getStub(): Stub
    {
        return $this->stub;
    }
}
