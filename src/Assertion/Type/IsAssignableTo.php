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

/** @template-implements Assertion<non-empty-string> */
final class IsAssignableTo implements Assertion
{
    /** @var Assertion<non-empty-string>|Assertion<class-string> */
    private Assertion $assertion;

    public function __construct(private string $type)
    {
        (new IsType())
            ->assert($type);

        $this->assertion = $this->getAssertion($this->type);
    }

    /** @psalm-assert string $value*/
    public function assert(mixed $value, string $message = ''): void
    {
        (new IsType())
            ->assert($value);

        $this->assertion->assert($value);
    }

    /** @psalm-assert-if-true string $value */
    public function __invoke(mixed $value): bool
    {
        try {
            $this->assert($value);

            return true;
        } catch (AssertionFailed) {
            return false;
        }
    }

    /** @return Assertion<non-empty-string>|Assertion<class-string> */
    private function getAssertion(string $type): Assertion
    {
        if ((new IsUnion())($type)) {
            return new IsAssignableToUnion($type);
        }

        if ((new IsIntersection())($type)) {
            return new IsAssignableToIntersection($type);
        }

        if ((new IsClassOrInterface())($type)) {
            /** @psalm-var class-string $type */
            return new IsAssignableToClass($type);
        }

        return new IsAssignableToPrimitive($type);
    }
}
