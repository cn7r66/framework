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
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;

/** 
 * @template T
 * @template K of Provider
 */
final class ProviderBinder
{
    /** @var callable(K):T */
    private $create;

    /** 
     * @param K             $provider
     * @param callable(K):T $create
     */
    public function __construct(private $provider, callable $create)
    {
        (new IsAssignableTo(Provider::class))
            ->assert($provider::class);

        (new IsNotNull())
            ->assert(
                (new ReflectionFunction($create))->getReturnType(),
                '"Missing type hint on callback function."',
            );

        $this->create = $create;
    }

    /**
     * @template J of K
     * 
     * @param callable(J):J
     *  
     * @return T 
     */
    public function as(callable $configure)
    {
        return ($this->create)(
            $configure($this->provider)
        );
    }
}
