<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

interface Injection
{
    public function inject(Container $container, mixed $instance): mixed;

    public function getPriority(): int;
}
