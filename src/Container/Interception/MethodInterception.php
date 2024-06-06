<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Container\Interception;
use Vivarium\Container\Reflection\InstanceMethod;
use Vivarium\Container\Reflection\MethodCall;
use Vivarium\Equality\Equality;

interface MethodInterception extends Interception, Equality
{
    public function getMethodCall(): InstanceMethod;

    /** @param callable(MethodCall):InstanceMethod $configure */
    public function configure(callable $configure): self;
}
