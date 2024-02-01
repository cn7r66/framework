<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Conditional;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;

use function sprintf;

/** @template-implements Assertion<null> */
final class IsNotNull implements Assertion
{
    /** @psalm-assert !null $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected value to be not null.',
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true !null $value */
    public function __invoke(mixed $value): bool
    {
        return $value !== null;
    }
}
