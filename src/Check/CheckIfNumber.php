<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool isGreaterOrEqualThan(int|float $number , int|float $compare)
 * @method static bool isGreaterThan(int|float $number , int|float $compare)
 * @method static bool isInClosedRange(int|float $number , int|float $min, int|float $max)
 * @method static bool isInHalfOpenLeftRange(int|float $number , int|float $min, int|float $max)
 * @method static bool isInHalfOpenRightRange(int|float $number , int|float $min, int|float $max)
 * @method static bool isInOpenRange(int|float $number , int|float $min, int|float $max)
 * @method static bool isLessOrEqualThan(int|float $number , int|float $compare)
 * @method static bool isLessThan(int|float $number , int|float $compare)
 * @method static bool isOutOfClosedRange(int|float $number , int|float $min, int|float $max)
 * @method static bool isOutOfOpenRange(int|float $number , int|float $min, int|float $max)
 */
final class CheckIfNumber
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::numeric();
        }

        return static::$check->__call($name, $arguments);
    }
}
