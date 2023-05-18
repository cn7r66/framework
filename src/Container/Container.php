<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

interface Container
{
    public function get(Key $key): mixed;

    public function has(Key $key): bool;
}
