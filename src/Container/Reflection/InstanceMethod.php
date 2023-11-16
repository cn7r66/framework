<?php
/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Reflection;

use Vivarium\Container\Container;

interface InstanceMethod extends Method
{
    public function invoke(Container $container, object $instance): mixed;
}
