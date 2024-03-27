<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Container\Container;
use Vivarium\Container\Reflection\MethodCall;

final class ImmutableMethodInterception implements MethodInterception
{
    public function __construct(private MethodCall $method)
    {
    }

    public function intercept(Container $container, object $instance): object
    {
        (new HasMethod($this->method->getName()))
            ->assert($instance);

        $return = $this->method->invoke($container, $instance);

        (new IsAssignableTo($instance::class))
            ->assert($return::class);

        return $return;
    }

    public function configure(callable $configure): self
    {
        $interception         = clone $this;
        $interception->method = $configure($this->method);

        return $interception;
    }
}
