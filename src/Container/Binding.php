<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

use Vivarium\Equality\Equality;

interface Binding extends Equality
{
    public const GLOBAL = '$GLOBAL';

    public const DEFAULT = '$DEFAULT';

    /** @return non-empty-string */
    public function getId(): string;

    /** @return non-empty-string */
    public function getTag(): string;

    /** @return non-empty-string */
    public function getContext(): string;
}
