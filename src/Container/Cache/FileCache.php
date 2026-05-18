<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use Cache;

final class FileCache implements Cache
{
    public function __construct(private string $path)
    {
    }
}
