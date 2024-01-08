<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Container;
use Vivarium\Container\Injection\MethodCall;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\InstanceMethod;
use Vivarium\Container\Reflection\StaticMethod;

final class Factory implements StaticMethod, Provider
{
    private Binding $factory;

    private InstanceMethod $method;

    private string|null $requester;

    public function __construct(
        string $class,
        string $method,
        string $tag = Binding::DEFAULT,
        string $context = Binding::GLOBAL,
    ) {
        $this->factory = new ClassBinding(
            $class,
            $context,
            $tag,
        );

        $this->method    = new MethodCall($class, $method);
        $this->requester = null;
    }

    public function provide(Container $container, string|null $requester = null): mixed
    {
        return $this->getMethod($requester)->invoke(
            $container,
            $container->get($this->factory),
        );
    }

    public function requesterOn(string $parameter): self
    {
        $factory            = clone $this;
        $factory->requester = $parameter;

        return $factory;
    }

    private function getMethod(string $requester): InstanceMethod
    {
        return $this->requester === null ?
            $this->method : $this->method
                ->bindParameter($this->requester)
                ->toInstance($requester);
    }

    public function getClass(): string
    {
        // TODO: Implement getClass() method.
    }

    public function getName(): string
    {
        // TODO: Implement getName() method.
    }

    public function bindParameter(string $parameter): Binder
    {
        // TODO: Implement bindParameter() method.
    }

    public function getParameter(string $parameter): Provider
    {
        // TODO: Implement getParameter() method.
    }

    public function hasParameter(string $parameter): bool
    {
        // TODO: Implement hasParameter() method.
    }

    public function getArguments(): Sequence
    {
        // TODO: Implement getArguments() method.
    }

    public function getArgumentsValue(Container $container): Sequence
    {
        // TODO: Implement getArgumentsValue() method.
    }

    public function invoke(Container $container): mixed
    {
        // TODO: Implement invoke() method.
    }
}
