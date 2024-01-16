<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Comparator\Priority;

interface Definition extends Provider
{
    public function bindConstructorFactory(string $class, string $method, string $tag, string $context): self;

    public function bindConstructorStaticFactory(string $class, string $method, string $tag, string $context): self;

    /** @return Binder<self> */
    public function bindParameter(string $parameter): Binder;

    /** @return Binder<self> */
    public function bindProperty(string $property): Binder;

    /** callable(InstanceMethod): InstanceMethod */
    public function bindMethod(string $method, callable|null $define = null, int $priority = Priority::NORMAL): self;

    /** callable(InstanceMethod): InstanceMethod */
    public function bindImmutableMethod(
        string $method,
        callable|null $define = null,
        int $priority = Priority::NORMAL,
    ): self;
}
