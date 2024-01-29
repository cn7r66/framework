<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\SimpleStub;

final class StaticStub
{
    public static function get(Stub $stub): SimpleStub
    {
        return new SimpleStub($stub);
    } 
}
