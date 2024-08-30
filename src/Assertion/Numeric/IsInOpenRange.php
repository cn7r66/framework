<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Numeric;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsNumeric;
use Vivarium\Type\Type;

use function sprintf;

/**
 * @template T as int|float
 * @template-implements Assertion<T>
 */
final class IsInOpenRange implements Assertion
{
    /**
     * @param T $min
     * @param T $max
     */
    public function __construct(private $min, private $max)
    {
        (new IsLessThan($max))
            ->assert($min, 'Lower bound must be lower than upper bound. Got (%1$s, %2$s).');
    }

    /** @psalm-assert T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected number to be in open range (%2$s, %3$s). Got %s.',
                Type::toLiteral($value),
                Type::toLiteral($this->min),
                Type::toLiteral($this->max),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert T $value */
    public function __invoke(mixed $value): bool
    {
        (new IsNumeric())
            ->assert($value);

        return ($this->min < $value) && ($value < $this->max);
    }
}
