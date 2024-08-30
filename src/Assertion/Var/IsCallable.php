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

use function is_callable;
use function sprintf;

/** @template-implements Assertion<callable> */
final class IsCallable implements Assertion
{
    /** @psalm-assert callable $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected value to be callable. Got %2$s.',
                Type::toLiteral($value),
                Type::toString($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true callable $value */
    public function __invoke(mixed $value): bool
    {
        return is_callable($value);
    }
}
