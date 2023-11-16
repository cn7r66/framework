<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use Vivarium\Assertion\String\IsClassOrInterface;

final class ClassBinding extends BaseBinding
{
    public function __construct(string $id, string $tag = self::DEFAULT, string $context = self::GLOBAL)
    {
        (new IsClassOrInterface())
            ->assert($id);

        parent::__construct($id, $tag, $context);
    }

    /** @return class-string */
    public function getId() : string
    {
        return parent::getId();
    }
}
