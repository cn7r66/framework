<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;

final class Prototype implements Provider
{
    /** @var Sequence<Provider> */
    private Sequence $arguments;

    /** @param class-string $class */
    public function __construct(private string $class)
    {
        $this->arguments = new ArraySequence();
    }

    public function provide(Container $container)
    {
        $arguments = [];
        foreach ($this->arguments as $argument) {
            $arguments[] = $argument->provide($container);
        }

        return new ($this->class)(...$arguments);
    }

    public function addParameter(Provider $provider): self
    {
        $prototype            = clone $this;
        $prototype->arguments = $prototype->arguments->add($provider);

        return $prototype;
    }
}
