<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use ReflectionClass;
use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Type\Type;

use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsAssignableToPrimitive implements Assertion
{
    public function __construct(private string $primitive)
    {
        (new IsPrimitive())
            ->assert($primitive);
    }

    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected type to be assignable to primitive type %2$s. Got %1$s.',
                Type::toLiteral($value),
                Type::toLiteral($this->primitive),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true non-empty-string $value */
    public function __invoke(mixed $value): bool
    {
        (new IsBasicType())
            ->assert($value);

        if ((new IsPrimitive())($value)) {
            if ($this->primitive === 'float' && $value === 'int') {
                return true;
            }

            return $this->primitive === $value;
        }

        /** @psalm-var class-string $value */

        if ($this->primitive === 'object') {
            return true;
        }

        if ($this->primitive === 'string') {
            return (new ReflectionClass($value))
                ->hasMethod('__toString');
        }

        if ($this->primitive === 'callable') {
            return (new ReflectionClass($value))
                ->hasMethod('__invoke');
        }

        return false;
    }
}
