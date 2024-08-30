<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Type\Type;

use function explode;
use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsAssignableToUnion implements Assertion
{
    public function __construct(private string $union)
    {
        (new IsUnion())
            ->assert($union);
    }

    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected type to be assignable to union %2$s Got %1$s.',
                Type::toLiteral($value),
                Type::toLiteral($this->union),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true non-empty-string $value
     */
    public function __invoke(mixed $value): bool
    {
        (new IsBasicType())
            ->assert($value);

        foreach (explode('|', $this->union) as $type) {
            if ((new IsAssignableTo($type))($value)) {
                return true;
            }
        }

        return false;
    }
}
