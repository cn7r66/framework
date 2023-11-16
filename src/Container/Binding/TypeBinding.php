<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use Vivarium\Assertion\String\IsType;

final class TypeBinding extends BaseBinding
{
    public function __construct(string $id, string $tag = self::DEFAULT, string $context = self::GLOBAL)
    {
        (new IsType())
            ->assert($id);

        parent::__construct($id, $tag, $context);
    }
}
