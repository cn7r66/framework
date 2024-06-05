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
use Vivarium\Assertion\String\IsClass;
use Vivarium\Assertion\String\IsType;
use Vivarium\Container\Binding;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Provider\StaticFactory;

/** @template T */
final class Binder
{
    /** @var callable(Provider):T */
    private $create;

     /** @param callable(Provider): T $create */
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

    public function toFactory(
        string $class,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ): MethodBinder {
        return new MethodBinder(function (string $method, callable $configure) use ($class, $tag, $context) {
            return $this->toProvider(
                (new Factory(
                    $class,
                    $method,
                    $tag,
                    $context,
                ))->configure($configure),
            );
        });
    }

    public function toStaticFactory(string $class): MethodBinder
    {
        (new IsClass())
            ->assert($class);

        return new MethodBinder(function (string $method, callable $configure) use ($class) {
            return $this->toProvider(
                (new StaticFactory($class, $method))
                    ->configure($configure),
            );
        });
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
