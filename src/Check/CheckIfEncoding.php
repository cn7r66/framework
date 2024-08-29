<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool isEncoding(string $encoding)
 * @method static bool isRegexEncoding(string $encoding)
 * @method static bool isSystemEncoding(string $encoding)
 */
final class CheckIfEncoding
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::encoding();
        }

        return static::$check->__call($name, $arguments);
    }
}
