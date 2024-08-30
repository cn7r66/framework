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
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Equality\EqualsBuilder;
use Vivarium\Type\Type;

use function sprintf;

/**
 * @template T
 * @template-implements Assertion<T>
 */
final class IsEqualsTo implements Assertion
{
    /** @param T $compare */
    public function __construct(private $compare)
    {
    }

    /** @psalm-assert T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected value to be equals to %2$s. Got %s.',
                Type::toLiteral($value),
                Type::toLiteral($this->compare),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true T $value */
    public function __invoke(mixed $value): bool
    {
        return (new EqualsBuilder())
            ->append($value, $this->compare)
            ->isEquals();
    }
}
