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
use Vivarium\Type\Type;

use function get_class;
use function sprintf;

/**
 * @template T
 * @template-implements Assertion<T>
 */
final class Not implements Assertion
{
    /** @param Assertion<T> $assertion */
    public function __construct(private Assertion $assertion)
    {
    }

    /** @psalm-assert !T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Failed negating the assertion %2$s with value %s.',
                Type::toLiteral($value),
                Type::toLiteral(get_class($this->assertion)),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true !T $value */
    public function __invoke(mixed $value): bool
    {
        return ! ($this->assertion)($value);
    }
}
