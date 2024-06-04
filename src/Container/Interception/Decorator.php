<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Interception;

use Vivarium\Container\Container;
use Vivarium\Container\Interception;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

final class Decorator implements Interception, Equality
{
    private Prototype $prototype;

    public function __construct(private string $class, private string $parameter)
    {
        $this->prototype = new Prototype($class);
    }

    /** @param callable(Definition):Definition $configure */
    public function configure(callable $configure): self
    {
        $decorator            = clone $this;
        $decorator->prototype = $configure($decorator->prototype);

        return $decorator;
    }

    public function getParameter(): string
    {
        return $this->parameter;
    }

    public function intercept(Container $container, object $instance): object 
    {
        return $this->prototype->bindParameter($this->parameter)
                               ->toInstance($instance)
                               ->provide($container);
    }

    public function equals(object $object): bool 
    { 
        if (! $object instanceof Decorator) {
            return false;
        }

        if ($object === $this) {
            return true;
        }

        return (new EqualsBuilder())
            ->append($this->class, $object->class)
            ->append($this->parameter, $object->parameter)
            ->isEquals();

    }

    public function hash(): string 
    { 
        return (new HashBuilder())
            ->append($this->class)
            ->append($this->parameter)
            ->getHashCode();
    }
}
