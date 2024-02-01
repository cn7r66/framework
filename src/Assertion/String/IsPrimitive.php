<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Assertion\String;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Comparison\IsOneOf;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\Type\IsString;

use function sprintf;

/** @template-implements Assertion<'int'|'float'|'string'|'array'|'callable'|'object'> */
final class IsPrimitive implements Assertion
{
    /** @psalm-assert 'int'|'float'|'string'|'array'|'callable'|'object' $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected string to be a primitive type. Got %s.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true 'int'|'float'|'string'|'array'|'callable'|'object' $value
     */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        return (new IsOneOf(
            'int',
            'float',
            'string',
            'array',
            'callable',
            'object',
            'bool',
        ))($value);
    }
}
