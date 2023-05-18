<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container\Provider;

use RuntimeException;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class Prototype implements Provider
{
    /** @var Sequence<Provider> */
    private Sequence $arguments;

    /** @param class-string $class */
    public function __construct(private string $class)
    {
        /** @psalm-var ArraySequence<Provider> */
        $this->arguments = new ArraySequence();
    }

    public function provide(Container $container): mixed
    {
        throw new RuntimeException('Not implemented yet.');
    }

    public function addParameter(Provider $provider): self
    {
        throw new RuntimeException('Not implemented yet.');
    }

    public function getKey(): Key
    {
        throw new RuntimeException('Not implemented yet.');
    }
}
