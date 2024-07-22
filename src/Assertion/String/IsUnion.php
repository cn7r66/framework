<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\String;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Each;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\Type\IsString;

use function count;
use function explode;
use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsUnion implements Assertion
{
    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        (new IsString())
            ->assert($value);

        try {
            $types = array_map(function ($type) {
                return trim(str_replace(['(', ')'], '', $type));
            }, explode('|', $value));

            if (count($types) <= 1) {
                throw new AssertionFailed('Union must be composed at least by two elements.');
            }

            (new Each(
                new Either(
                    new IsBasicType(),
                    new IsIntersection()
                )
            ))->assert($types);
        } catch (AssertionFailed $ex) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected string to be union. Got %s.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message, $ex->getCode(), $ex);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true non-empty-string $value
     */
    public function __invoke(mixed $value): bool
    {
        try {
            $this->assert($value);

            return true;
        } catch (AssertionFailed) {
            return false;
        }
    }
}
