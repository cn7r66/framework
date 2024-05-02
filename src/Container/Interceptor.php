<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use ReflectionFunction;
use Vivarium\Assertion\Conditional\IsNotNull;

/**
 * @template T
 */
final class Interceptor
{
    /** @var callable(Interception):T */
    private $create;

        /** @param callable(Interception): T $create */
    public function __construct(callable $create)
    {
        (new IsNotNull())
            ->assert(
                (new ReflectionFunction($create))->getReturnType(),
                '"Missing type hint on callback function."',
            );

        $this->create = $create;
    }

    public function withMethod(callable $configure) { }

    public function withImmutableMethod(callable $configure) { }

    public function withUniqueMethod(callable $configure) { }

    public function withUniqueImmutableMethod(callable $configure) { }

    public function withInterception(Interception $interception) { }

    public function withUniqueInterception(Interception $interception) { }

}
