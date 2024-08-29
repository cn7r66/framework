<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool isEqualsTo(mixed $first, mixed $second)
 * @method static bool isOneOf(mixed $element, array $elements)
 * @method static bool isSameOf(mixed $element, array $elements)
 */
final class CheckIfElement
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::comparison();
        }

        return static::$check->__call($name, $arguments);
    }
}
