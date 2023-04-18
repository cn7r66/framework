<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container;

interface Provider
{
    public function provide(Container $container): mixed;

    public function getKey(): Key;
}
