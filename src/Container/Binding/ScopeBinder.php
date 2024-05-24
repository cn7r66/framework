<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use ReflectionFunction;
use Vivarium\Assertion\Conditional\IsNotNull;

/** @template T */
final class ScopeBinder
{
    /** @var callable(Scope):T */
    private $create;

    /** @param callable(Scope): T $create */
    public function __construct(callable $create)
    {
        (new IsNotNull())
            ->assert(
                (new ReflectionFunction($create))->getReturnType(),
                '"Missing type hint on callback function."',
            );

        $this->create = $create;
    }

    /** @return T */
    public function service()
    {
        return ($this->create)(Scope::SERVICE);
    }

    /** @return T */
    public function cloneable()
    {
        return ($this->create)(Scope::CLONEABLE);
    }

    /** @return T */
    public function prototype()
    {
        return ($this->create)(Scope::PROTOTYPE);
    }

}
