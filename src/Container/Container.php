<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Psr\Container\ContainerInterface;

interface Container extends ContainerInterface
{
    public function get(string|Binding $request): mixed;

    public function has(string|Binding $request): bool;
}
