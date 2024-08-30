<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Var;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Type\Type;

use function is_string;
use function sprintf;

/** @template-implements Assertion<string> */
final class IsString implements Assertion
{
    /** @psalm-assert string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected value to be string. Got %2$s.',
                Type::toLiteral($value),
                Type::toString($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true string $value */
    public function __invoke(mixed $value): bool
    {
        return is_string($value);
    }
}
