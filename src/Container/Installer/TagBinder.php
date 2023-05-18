<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;

final class TagBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key,
    ) {
    }

    /** @param non-empty-string $tag */
    public function withTag(string $tag): ConcreteBinder
    {
        return new ConcreteBinder(
            $this->installer,
            new Key(
                $this->key->getType(),
                $this->key->getContext(),
                $tag,
            ),
        );
    }

    public function withoutTag(): ConcreteBinder
    {
        return new ConcreteBinder(
            $this->installer,
            $this->key,
        );
    }
}
