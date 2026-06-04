<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Type;

use Closure;
use Vivarium\Type\Exception\NotAType;

use function array_keys;
use function gettype;
use function in_array;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;

final class Type
{
    public const INT      = 'int';
    public const FLOAT    = 'float';
    public const STRING   = 'string';
    public const BOOL     = 'bool';
    public const ARRAY    = 'array';
    public const OBJECT   = 'object';
    public const CALLABLE = 'callable';
    public const MIXED    = 'mixed';
    public const NULL     = 'null';

    private const ALIASES = [
        'integer' => self::INT,
        'double'  => self::FLOAT,
        'boolean' => self::BOOL,
        'NULL'    => self::NULL,
    ];

    /** @return list<string> */
    public static function canonical(): array
    {
        return [
            self::INT,
            self::FLOAT,
            self::STRING,
            self::BOOL,
            self::ARRAY,
            self::OBJECT,
            self::CALLABLE,
            self::MIXED,
        ];
    }

    /** @return list<string> */
    public static function expanded(): array
    {
        return [
            ...self::canonical(),
            ...array_keys(self::ALIASES),
        ];
    }

    public static function normalize(string $type): string
    {
        if (! in_array($type, self::expanded())) {
            throw new NotAType($type);
        }

        return self::ALIASES[$type] ?? $type;
    }

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

        if (is_callable($value) || $value instanceof Closure) {
            return self::CALLABLE;
        }

        if (is_object($value)) {
            return '"' . $value::class . '"';
        }

        if (is_string($value)) {
            return '"' . $value . '"';
        }

        return (string) $value;
    }

    public static function toString(mixed $value): string
    {
        $type = gettype($value);

        if ($type === self::OBJECT && $value instanceof Closure) {
            return self::CALLABLE;
        }

        return self::normalize($type);
    }
}
