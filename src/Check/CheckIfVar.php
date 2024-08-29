<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool IsArray(mixed $var)
 * @method static bool IsBoolean(mixed $var)
 * @method static bool IsCallable(mixed $var)
 * @method static bool IsFloat(mixed $var)
 * @method static bool IsInteger(mixed $var)
 * @method static bool IsNumeric(mixed $var)
 * @method static bool IsObject(mixed $var)
 * @method static bool IsString(mixed $var)
 */
final class CheckIfVar
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::var();
        }

        return static::$check->__call($name, $arguments);
    }
}
