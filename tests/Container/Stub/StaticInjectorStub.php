<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class StaticInjectorStub
{
    public static function get(Stub $stub): Stub
    {
        $stub->setInt(42);

        return $stub;
    }
}
