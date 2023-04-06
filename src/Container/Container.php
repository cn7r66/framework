<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container;

interface Container
{
    /**
     * @param Key $key
     *
     * @return object|array|string|int|float|bool
     */
    public function get(Key $key): object|array|string|int|float|bool;

    public function has(Key $key): bool;
}