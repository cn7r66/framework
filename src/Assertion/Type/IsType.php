<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function sprintf;

/** @template-implements Assertion<non-empty-string|'int'|'float'|'string'|'array'|'callable'|'object'|class-string> */
final class IsType implements Assertion
{
    /** @psalm-assert non-empty-string|'int'|'float'|'string'|'array'|'callable'|'object'|class-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected string to be a primitive, class, interface, union or intersection. Got %s.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true non-empty-string|'int'|'float'|'string'|'array'|'callable'|'object'|class-string $value
     */
    public function __invoke(mixed $value): bool
    {
        return (new Either(
            new IsBasicType(),
            new Either(
                new IsUnion(),
                new IsIntersection(),
            ),
        ))($value);
    }
}
