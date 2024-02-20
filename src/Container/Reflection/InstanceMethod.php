<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Container\Container;
use Vivarium\Container\Injection;

interface InstanceMethod extends Method
{
    public function invoke(Container $container, object $instance): mixed;
}
