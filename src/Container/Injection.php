<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface Injection
{
    /**
     * @param T $instance
     *
     * @return T
     *
     * @template T of object
     */
    public function inject(Container $container, object $instance): object;
}
