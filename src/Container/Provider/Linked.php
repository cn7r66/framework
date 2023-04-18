<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Linked implements Provider
{
    private Key $source;

    private Key $target;

    public function __construct(Key $source, Key $target)
    {
        $this->source = $source;
        $this->target = $target;
    }

    public function provide(Container $container): mixed
    {
        return $container->get(new Key(
            $this->target->getType(),
            $this->source->getContext(),
            $this->target->getTag()
        ));
    }
}
