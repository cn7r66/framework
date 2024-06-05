<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Container\Container;

final class MutableMethodInterception extends BaseMethodInterception
{
    public function intercept(Container $container, object $instance): object
    {
        (new HasMethod($this->getMethodCall()->getName()))
            ->assert($instance);

        $this->getMethodCall()->invoke($container, $instance);

        return $instance;
    }
}
