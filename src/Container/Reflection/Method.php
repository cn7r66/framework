<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Container\Binder;
use Vivarium\Container\Provider;

interface Method
{
    public function getName(): string;

    /** @return Binder<self> */
    public function bindParameter(string $parameter): Binder;

    public function getParameter(string $parameter): Provider;

    public function hasParameter(string $parameter): bool;
}
