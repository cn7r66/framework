<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface ConfigurableSolver extends Solver
{
    /** @return Binder<self> */
    public function bind(string $type, string $tag, string $context): Binder;

    /** @return Binder<self> */
    public function rebind(string $type, string $tag, string $context): Binder;

    /** callable(Definition) */
    public function define(string $class, callable $define, string $tag, string $context): self;

    /** callable(Injectable) */
    public function inject(string $type, callable $inject, string $tag, string $context): self;

    /**
     * @template T of Provider
     *
     * @param callable(T): Provider $extend
     */
    public function extend(string $type, callable $extend, string $tag, string $context): self;

    public function decorate();
}
