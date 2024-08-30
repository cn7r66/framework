<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Boolean;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsBoolean;
use Vivarium\Type\Type;

use function sprintf;

/** @template-implements Assertion<true> */
final class IsTrue implements Assertion
{
    /** @psalm-assert true $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected boolean to be true. Got %s.',
                Type::toLiteral($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true true $value */
    public function __invoke(mixed $value): bool
    {
        (new IsBoolean())
            ->assert($value);

        return $value === true;
    }
}
