<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Check;

/**
 * @method static bool ImplementsInterface(string $type, string $interface)
 * @method static bool IsAssignableTo(string $type, string $target)
 * @method static bool IsAssignableToClass(string $type, string $class)
 * @method static bool IsAssignableToIntersection(string $type, string $intersection)
 * @method static bool IsAssignableToPrimitive(string $type, string $primitive)
 * @method static bool IsAssignableToUnion(string $type, string $union)
 * @method static bool IsSubclassOf(string $type, string $class)
 * @method static bool IsBasicType(string $type)
 * @method static bool IsClass(string $type)
 * @method static bool IsClassOrInterface(string $type)
 * @method static bool IsInterface(string $type)
 * @method static bool IsIntersection(string $type)
 * @method static bool IsNamespace(string $type)
 * @method static bool IsPrimitive(string $type)
 * @method static bool IsType(string $type)
 * @method static bool IsUnion(string $type)
 */
final class CheckIfType
{
    private static Check|null $check = null;

    /** @param array<mixed> $arguments */
    public static function __callStatic(string $name, array $arguments): bool
    {
        if (static::$check === null) {
            static::$check = Check::type();
        }

        return static::$check->__call($name, $arguments);
    }
}
