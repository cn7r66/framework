<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface Interception
{
    /**
     * @param T $instance
     *
     * @return T
     *
     * @template T of object
     */
    public function intercept(Container $container, object $instance): object;
}
