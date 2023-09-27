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

    public function __construct(private Key $key)
    {
        $this->arguments = new ArraySequence();
    }

    public function provide(Container $container): mixed
    {
        $injections = [];
        foreach ($this->arguments as $argument) {
            $injections[] = $argument->provide($container);
        }

        return new ($this->key->getType())(...$injections);
    }

    public function withArgument(Provider $provider): self
    {
        $prototype = clone $this;
        $prototype->arguments = $prototype->arguments->add($provider);

        return $prototype;
    }

    /** @return Sequence<Provider> */
    public function getArguments(): Sequence
    {
        return $this->arguments;
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}
