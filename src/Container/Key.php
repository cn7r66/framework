<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container;

use Vivarium\Assertion\Comparison\IsOneOf;
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
    const GLOBAL = '$GLOBAL';

    const DEFAULT = '$DEFAULT';

    private string $type;

    private string $context;

    private string $tag;

    public function __construct(string $type, string $context = Key::GLOBAL, string $tag = Key::DEFAULT)
    {
        (new IsType())
            ->assert($type);

        (new Either(
            new IsSameOf(self::GLOBAL),
            new Either(
                new IsClassOrInterface(),
                new IsNamespace()
            )
        ))->assert($context, 'Expected string to be $GLOBAL, class, interface or namespace. Got %s.');

        $this->type    = $type;
        $this->context = $context;
        $this->tag     = $tag;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getContext(): string
    {
        return $this->context;
    }

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
