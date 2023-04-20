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
     */
    public function get(Key $key): mixed;

    public function has(Key $key): bool;
}