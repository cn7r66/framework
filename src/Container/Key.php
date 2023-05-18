<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

use Vivarium\Assertion\Comparison\IsSameOf;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsNamespace;
use Vivarium\Assertion\String\IsType;
use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

final class Key implements Equality
{
    public const GLOBAL = '$GLOBAL';

    public const DEFAULT = '$DEFAULT';

    public function __construct(
        private string $type,
        private string $context = self::GLOBAL,
        private string $tag = self::DEFAULT,
    ) {
        (new IsType())
            ->assert($type);

        (new Either(
            new IsSameOf(self::GLOBAL),
            new Either(
                new IsClassOrInterface(),
                new IsNamespace(),
            ),
        ))->assert($context, 'Expected string to be $GLOBAL, class, interface or namespace. Got %s.');
    }

    /** @return non-empty-string|class-string */
    public function getType(): string
    {
        return $this->type;
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
            ->append($this->type, $object->type)
            ->append($this->context, $object->context)
            ->append($this->tag, $object->tag)
            ->isEquals();
    }

    public function hash(): string
    {
        return (new HashBuilder())
            ->append($this->type)
            ->append($this->context)
            ->append($this->tag)
            ->getHashCode();
    }
}
