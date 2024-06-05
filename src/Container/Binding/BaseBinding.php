<?php

declare(strict_types=1);

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
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binding;
use Vivarium\Container\Exception\CannotBeWidened;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

use function array_reverse;
use function strrpos;
use function substr;

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

    /** @return Sequence<Binding> */
    public function hierarchy(): Sequence
    {
        return $this->expand([$this]);
    }

    public function widen(): Binding
    {
        if (! $this->couldBeWidened()) {
            throw new CannotBeWidened($this);
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

    /**
     * @param array<Binding> $bindings
     *
     * @return Sequence<Binding>
     */
    protected function expand(array $bindings): Sequence
    {
        $hierarchy = [];
        foreach ($bindings as $binding) {
            $hierarchy[] = $binding;
            while ($binding->couldBeWidened()) {
                $binding     = $binding->widen();
                $hierarchy[] = $binding;
            }
        }

        return ArraySequence::fromArray(
            array_reverse($hierarchy),
        );
    }
}
