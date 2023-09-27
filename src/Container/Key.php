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
use Vivarium\Assertion\String\IsNotEmpty;
use Vivarium\Assertion\String\IsType;
use Vivarium\Equality\Equality;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

final class Key implements Equality
{
    /** @var string */
    public const GLOBAL = '$GLOBAL';

    /** @var string */
    public const DEFAULT = '$DEFAULT';

    /** @var non-empty-string|class-string */
    private string $type;

    /** @var Key::GLOBAL|non-empty-string|class-string */
    private string $context;

    /** @var non-empty-string */
    private string $tag;

    public function __construct(string $type, string $context = self::GLOBAL, string $tag = self::DEFAULT) {
        (new IsType())
            ->assert($type);

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

        $this->type    = $type;
        $this->context = $context;
        $this->tag     = $tag;
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
