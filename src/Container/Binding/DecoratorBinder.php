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
use Vivarium\Comparator\Priority;
use Vivarium\Container\Interception\Decorator;
use Vivarium\Container\Provider;

/** 
 * @template T
 * @template K of Provider
 */
final class DecoratorBinder
{
    /** @var callable(Decorator, int): T */
    private $create;

    /** @param callable(Decorator, int): T $name */
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
    public function with(
        string $class, 
        string $parameter, 
        callable|null $configure = null, 
        int $priority = Priority::NORMAL)
    {
        $decorator = new Decorator($class, $parameter);
        if ($configure !== null) {
            $decorator = $decorator->configure($configure);
        }

        return ($this->create)($decorator, $priority);
    }
}
