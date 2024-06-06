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
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Comparator\Priority;
use Vivarium\Container\Interception;
use Vivarium\Container\Interception\ImmutableMethodInterception;
use Vivarium\Container\Interception\MutableMethodInterception;
use Vivarium\Container\Reflection\MethodCall;

/** @template T */
final class InterceptionBinder
{
    /** @var callable(Interception):T */
    private $create;

    /** @param callable(Interception): T $create */
    public function __construct(private string $class, callable $create)
    {
        (new IsClassOrInterface())
            ->assert($class);

        (new IsNotNull())
            ->assert(
                (new ReflectionFunction($create))->getReturnType(),
                '"Missing type hint on callback function."',
            );

        $this->create = $create;
    }

    /**
     * @param callable(InstanceMethod): InstanceMethod $define
     *
     * @return T
     */
    public function withMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL)
    {
        return $this->withInterception(
            new MutableMethodInterception(
                $this->bindMethodCall($method, $define),
            ),
            $priority,
        );
    }

    /**
     * @param callable(InstanceMethod): InstanceMethod $define
     *
     * @return T
     */
    public function withImmutableMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL)
    {
        return $this->withInterception(
            new ImmutableMethodInterception(
                $this->bindMethodCall($method, $define),
            ),
            $priority,
        );
    }

    /** @return T */
    public function withInterception(Interception $interception, int $priority = Priority::NORMAL)
    {
        return ($this->create)($interception, $priority);
    }

    private function bindMethodCall(string $method, callable|null $define = null): MethodCall
    {
        $call = new MethodCall($this->class, $method);

        return $define !== null ? $define($call) : $call;
    }
}
