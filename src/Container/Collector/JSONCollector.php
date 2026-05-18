<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Collector;

use Vivarium\Container\Collector;

final class JSONCollector implements Collector
{
    public function __construct(private string $path)
    {
    }
}
