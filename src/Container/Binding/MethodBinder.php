<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use Vivarium\Container\Reflection\CreationalMethod;

/** @template T */
final class MethodBinder
{
    /** callable(string, callable(CreationalMethod)): T */
    private $create;

    /** @param callable(Provider): T $name */
    public function __construct(callable $create)
    {
        $this->create = $create;
    }

    /** @return T */
    public function method(string $method, callable|null $configure = null)
    {
        if ($configure === null) {
            $configure = static fn (CreationalMethod $method) => $method;
        }

        return ($this->create)($method, $configure);
    }
}
