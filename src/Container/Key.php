<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

use RuntimeException;
use Vivarium\Assertion\Comparison\IsSameOf;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsNamespace;
use Vivarium\Assertion\String\IsNotEmpty;
use Vivarium\Assertion\String\IsType;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Equality\HashBuilder;

use function array_merge;
use function class_exists;
use function class_implements;
use function count;
use function get_parent_class;
use function str_contains;
use function strrpos;
use function substr;

final class Key implements Binding
{




    public function widen(): Key
    {
        if (! $this->couldBeWidened()) {
            throw new RuntimeException();
        }

        if ($this->tag !== self::DEFAULT) {
            return new Key(
                $this->type,
                $this->context,
                self::DEFAULT,
            );
        }

        $parent = str_contains($this->context, '\\') ?
            substr($this->context, 0, strrpos($this->context, '\\')) : self::GLOBAL;

        return new Key(
            $this->type,
            $parent,
            $this->tag,
        );
    }

    public function couldBeWidened(): bool
    {
        return $this->tag !== self::DEFAULT ||
               $this->context !== self::GLOBAL;
    }

    public function hierarchy(): Sequence
    {
        $base = $this;

        $hierarchy = new ArraySequence($base);
        if (! class_exists($this->type)) {
            return $hierarchy;
        }

        while ($base->couldBeWidened()) {
            $base      = $base->widen();
            $hierarchy = $hierarchy->add($base);
        }

        $parent = $this->parents();
        while (count($parent) > 0) {
            foreach ($parent as $key) {
                $hierarchy = $hierarchy->add($key);
            }

            $parent = $this->widenList($parent);
        }

        return $hierarchy;
    }



    private function extends(): array
    {
        $extends = [];

        $extend = get_parent_class($this->type);
        while ($extend !== false) {
            $extends[] = new Key($extend, $this->context);

            $extend = get_parent_class($extend);
        }

        return $extends;
    }

    private function interfaces(): array
    {
        $interfaces = [];
        foreach (class_implements($this->type) as $interface) {
            $interfaces[] = new Key($interface, $this->context);
        }

        return $interfaces;
    }

    private function parents(): array
    {
        return array_merge(
            $this->extends(),
            $this->interfaces(),
        );
    }

    /**
     * @param array<Key> $keys
     *
     * @return array<Key>
     */
    private function widenList(array $keys): array
    {
        $widened = [];
        foreach ($keys as $key) {
            if (! $key->couldBeWidened()) {
                continue;
            }

            $widened[] = $key->widen();
        }

        return $widened;
    }
}
