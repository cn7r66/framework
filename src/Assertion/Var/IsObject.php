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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function gettype;
use function is_object;
use function sprintf;

/** @template-implements Assertion<object> */
final class IsObject implements Assertion
{
    /** @psalm-assert object $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected value to be object. Got %2$s.',
                (new TypeToString())($value),
                gettype($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true object $value */
    public function __invoke(mixed $value): bool
    {
        return is_object($value);
    }
}
