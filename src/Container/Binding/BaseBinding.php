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
use Vivarium\Container\Key;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

abstract class BaseBinding implements Binding
{
    /**
     * @param non-empty-string|class-string $id
     * @param non-empty-string|class-string $tag
     * @param non-empty-string|class-string $context
     */
    public function __construct(
        private string $id,
        private string $tag = self::DEFAULT,
        private string $context = self::GLOBAL,
    ) {
        /** @phpstan-var non-empty-string|class-string $context */
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

    /** @return non-empty-string|class-string */
    public function getId(): string
    {
        return $this->id;
    }

    /** @return non-empty-string|class-string */
    public function getContext(): string
    {
        return $this->context;
    }

    /** @return non-empty-string */
    public function getTag(): string
    {
        return $this->tag;
    }

    public function equals(object $object): bool
    {
        if ($object === $this) {
            return true;
        }

        if (! $object instanceof Key) {
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
