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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsBoolean;

use function sprintf;

/** @template-implements Assertion<false> */
final class IsFalse implements Assertion
{
    /** @psalm-assert false $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected boolean to be false. Got %s.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true false $value */
    public function __invoke(mixed $value): bool
    {
        (new IsBoolean())
            ->assert($value);

        return $value === false;
    }
}
