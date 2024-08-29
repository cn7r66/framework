<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Comparison;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Equality\EqualsBuilder;

use function is_object;
use function sprintf;

/**
 * @template T
 * @template-implements Assertion<T>
 */
final class IsOneOf implements Assertion
{
    /** @param array<T> $choices */
    public function __construct(private array $choices)
    {
    }

    /** @psalm-assert T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected value to be one of the values provided. Got %s.',
                is_object($value) ? 'different object' : (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true T $value */
    public function __invoke(mixed $value): bool
    {
        foreach ($this->choices as $choice) {
            if (
                (new EqualsBuilder())
                ->append($value, $choice)
                ->isEquals()
            ) {
                return true;
            }
        }

        return false;
    }
}
