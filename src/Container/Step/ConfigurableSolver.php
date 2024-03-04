<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Step;

use Vivarium\Container\Binder;
use Vivarium\Container\Interception;
use Vivarium\Container\Step;

interface ConfigurableSolver extends Step
{
    /** @return Binder<self> */
    public function bind(string $type, string $tag, string $context): Binder;

    /** @return Binder<self> */
    public function rebind(string $type, string $tag, string $context): Binder;

    /** callable(Definition) */
    public function define(string $class, callable $define, string $tag, string $context): self;

    /**
     * @param callable(T): Provider $extend
     *
     * @template T of Provider
     */
    public function extend(string $type, callable $extend, string $tag, string $context): self;

    /** callable(Injectable) */
    public function inject(string $type, callable $inject, string $tag, string $context): self;

    public function decorate(): self;

    public function intercept(Interception $interception): self;
}
