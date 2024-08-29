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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsArray;

use function sprintf;

/**
 * @template T
 * @template-implements Assertion<array<T>>
 */
final class Each implements Assertion
{
    /** @param Assertion<T> $assertion */
    public function __construct(private Assertion $assertion)
    {
    }

    /** @psalm-assert array<T> $value */
    public function assert(mixed $value, string $message = ''): void
    {
        (new IsArray())
            ->assert($value);

        foreach ($value as $key => $element) {
            try {
                $this->assertion
                    ->assert($element);
            } catch (AssertionFailed $ex) {
                $message = sprintf(
                    ! (new IsEmpty())($message) ?
                         $message : 'Element at index %2$s failed the assertion.',
                    (new TypeToString())($element),
                    (new TypeToString())($key),
                );

                throw new AssertionFailed($message, 0, $ex);
            }
        }
    }

    /** @psalm-assert-if-true array<T> $value */
    public function __invoke(mixed $value): bool
    {
        (new IsArray())->assert($value);

        /**
         * We know this is a mixed assignment, we are trying to assert the type of elements in the array
         *
         * @psalm-suppress  MixedAssignment
         */
        foreach ($value as $element) {
            if (! ($this->assertion)($element)) {
                return false;
            }
        }

        return true;
    }
}
