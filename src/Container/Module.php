<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Vivarium\Container\Binding\Binder;

interface Module
{
    /**
     * @template T of Binder
     *
     * @param T $binder
     *
     * @return T
     */
    public function configure(Binder $binder): Binder;
}
