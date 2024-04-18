<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Step;

use Vivarium\Container\Binder;
use Vivarium\Container\Binding;
use Vivarium\Container\Interception;
use Vivarium\Container\Interceptor;
use Vivarium\Container\Step;

interface ConfigurableSolver extends Step
{
    /** @return Binder<self> */
    public function bind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder;

    /** @return Binder<self> */
    public function rebind(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Binder;

    /** callable(Definition) */
    public function define(string $class, callable $define, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): self;

    /**
     * @param callable(T): Provider $extend
     *
     * @template T of Provider
     */
    public function extend(string $type, callable $extend, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): self;

    public function intercept(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL): Interceptor;

    public function decorate(): self;
}
