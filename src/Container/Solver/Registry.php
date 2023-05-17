<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Solver;

use OutOfBoundsException;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Key;

use function array_slice;
use function count;
use function explode;
use function implode;

/** @template T */
final class Registry
{
    /** @var Map<Key, T> */
    private Map $map;

    public function __construct()
    {
        /** @psalm-var HashMap<Key, T> */
        $this->map = new HashMap();
    }

    /** @param T $entry */
    public function add(Key $key, $entry): self
    {
        $registry      = clone $this;
        $registry->map = $registry->map->put($key, $entry);

        return $registry;
    }

    /** @return T */
    public function get(Key $key)
    {
        if ($key->getTag() !== Key::DEFAULT) {
            return $this->getExactly($key);
        }

        if ($key->getContext() !== Key::GLOBAL) {
            do {
                if ($this->hasExactly($key)) {
                    return $this->getExactly($key);
                }

                $key = $this->widen($key);
            } while ($key->getContext() !== Key::GLOBAL);
        }

        return $this->getExactly($key);
    }

    /** @return T */
    public function getExactly(Key $key)
    {
        if (! $this->hasExactly($key)) {
            throw new OutOfBoundsException('The provided key is not present.');
        }

        return $this->map->get($key);
    }

    public function has(Key $key): bool
    {
        if ($key->getTag() !== Key::DEFAULT) {
            return $this->hasExactly($key);
        }

        if ($key->getContext() !== Key::GLOBAL) {
            do {
                if ($this->hasExactly($key)) {
                    return true;
                }

                $key = $this->widen($key);
            } while ($key->getContext() !== Key::GLOBAL);
        }

        return $this->hasExactly($key);
    }

    public function hasExactly(Key $key): bool
    {
        return $this->map->containsKey($key);
    }

    private function widen(Key $key): Key
    {
        $context = explode('\\', $key->getContext());

        if (count($context) === 1) {
            return new Key($key->getType());
        }

        return new Key(
            $key->getType(),
            implode('\\', array_slice($context, 0, -1)),
        );
    }
}
