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
use function is_float;
use function sprintf;

/** @template-implements Assertion<float> */
final class IsFloat implements Assertion
{
    /** @psalm-assert float $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected value to be float. Got %2$s.',
                (new TypeToString())($value),
                gettype($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true float $value */
    public function __invoke(mixed $value): bool
    {
        return is_float($value);
    }
}
