<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binder;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;

interface Method
{
    public function getClass(): string;

    public function getName(): string;

    /** @return Binder<self> */
    public function bindParameter(string $parameter): Binder;

    public function getParameter(string $parameter): Provider;

    public function hasParameter(string $parameter): bool;

    public function makeAccessible(): self;

    public function isAccessible(): bool;

    /** @return Sequence<Provider> */
    public function getArguments(): Sequence;

    public function getArgumentsValue(Container $container): Sequence;
}
