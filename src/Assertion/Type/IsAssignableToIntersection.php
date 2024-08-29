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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function explode;
use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsAssignableToIntersection implements Assertion
{
    public function __construct(private string $intersection)
    {
        (new IsIntersection())
            ->assert($intersection);
    }

    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected class to be assignable to intersection %2$s Got %1$s.',
                (new TypeToString())($value),
                (new TypeToString())($this->intersection),
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

        foreach (explode('&', $this->intersection) as $type) {
            if (! (new IsAssignableTo($type))($value)) {
                return false;
            }
        }

        return true;
    }
}
