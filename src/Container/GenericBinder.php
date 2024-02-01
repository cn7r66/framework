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
use Vivarium\Assertion\String\IsType;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Instance;

/**
 * @template T
 * @template-implements Binder<T>
 */
final class GenericBinder implements Binder
{
    /** @var callable(Provider):T */
    private $create;

     /** @param callable $create(Provider): T */
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
    public function to(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL)
    {
        (new IsType())
            ->assert($type);

        return $this->toProvider(
            new ContainerCall(
                new Binding\TypeBinding(
                    $type,
                    $tag,
                    $context,
                ),
            ),
        );
    }

    /** @return T */
    public function toInstance(mixed $instance)
    {
        return $this->toProvider(
            new Instance($instance),
        );
    }

    /** @return T */
    public function toProvider(Provider $provider)
    {
        return ($this->create)($provider);
    }
}
