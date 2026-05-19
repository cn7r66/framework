<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Equality\Equality;

interface Method extends Equality
{
    public function getClass(): string;

    public function getName(): string;

    public function bindArgument(string $name): ProviderBinder;

    public function bindArgumentAtPosition(int $position): ProviderBinder;

    public function getArgument(string $name): Provider;

    public function hasArgument(string $name): bool;

    /** @return Sequence<Provider> */
    public function getArguments(string|null $class = null): Sequence;

    /** @return Sequence<mixed> */
    public function getArgumentsValue(Container $container, string|null $class = null): Sequence;
}
