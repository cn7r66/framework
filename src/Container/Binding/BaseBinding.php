<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use Vivarium\Assertion\Comparison\IsSameOf;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsNamespace;
use Vivarium\Assertion\String\IsNotEmpty;
use Vivarium\Container\Binding;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseBinding implements Binding
{
    public function __construct(
        private string $id,
        private string $tag = self::DEFAULT,
        private string $context = self::GLOBAL,
    ) {
        (new IsNotEmpty())
            ->assert($id);

        (new Either(
            new IsSameOf(self::GLOBAL),
            new Either(
                new IsClassOrInterface(),
                new IsNamespace(),
            ),
        ))->assert($context, 'Expected string to be $GLOBAL, class, interface or namespace. Got %s.');

        (new IsNotEmpty())
            ->assert($tag);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getContext(): string
    {
        return $this->context;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function widen(): Binding
    {
        if (! $this->couldBeWidened()) {
            throw new \RuntimeException();
        }

        if ($this->tag !== self::DEFAULT) {
            $binding      = clone $this;
            $binding->tag = Binding::DEFAULT;

            return $binding;
        }

        $pos = strrpos($this->context, '\\');

        $parent =  $pos !== false ?
            substr($this->context, 0, $pos) : self::GLOBAL;

        $binding          = clone $this;
        $binding->context = $parent;

        return $binding;
    }

    public function couldBeWidened(): bool
    {
        return $this->tag !== self::DEFAULT ||
               $this->context !== self::GLOBAL;
    }

    public function equals(object $object): bool
    {
        if ($object === $this) {
            return true;
        }

        if (! $object instanceof Binding) {
            return false;
        }

        return (new EqualsBuilder())
            ->append($this->id, $object->getId())
            ->append($this->tag, $object->getTag())
            ->append($this->context, $object->getContext())
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->id)
            ->append($this->tag)
            ->append($this->context)
            ->getHashCode();
    }
}
