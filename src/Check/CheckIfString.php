<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool contains(string $string, string $substring)
 * @method static bool endsWith(string $string, string $end)
 * @method static bool isEmpty(string $string)
 * @method static bool isLong(string $string, int $length, string $encoding = 'UTF-8')
 * @method static bool isLongAtLeast(string $string, int $length, string $encoding = 'UTF-8')
 * @method static bool isLongAtMax(string $string, int $length, string $encoding = 'UTF-8')
 * @method static bool isLongBetween(string $string, int $min, int $max, string $encoding = 'UTF-8')
 * @method static bool isNotEmpty(string $string)
 * @method static bool startsWith(string $string, string $start)
 */
final class CheckIfString
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::string();
        }

        return static::$check->__call($name, $arguments);
    }
}
