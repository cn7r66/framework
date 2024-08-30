<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Type;

use Closure;

use function gettype;
use function is_array;
use function is_object;
use function is_string;

final class Type
{
    public static function toLiteral(mixed $value): string
    {
        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if ($value === null) {
            return 'null';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_object($value)) {
            $value = $value::class;
        }

        if (is_string($value)) {
            return '"' . $value . '"';
        }

        return (string) $value;
    }

    public static function toString(mixed $value): string
    {
        $type = gettype($value);

        if ($type === 'double') {
            return 'float';
        }

        if ($type === 'boolean') {
            return 'bool';
        }

        if ($type === 'integer') {
            return 'int';
        }

        if ($type === 'NULL') {
            return 'null';
        }

        if ($type === 'object' && $value instanceof Closure) {
            return 'callable';
        }

        return $type;
    }
}
