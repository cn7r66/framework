<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Encoding;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\All;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Type\Type;

use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsEncoding implements Assertion
{
    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! ($this)($value)) {
            $message = sprintf(
                '%s is not a valid encoding.',
                Type::toLiteral($value),
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
        (new IsString())
            ->assert($value);

        return (new All(
            new IsSystemEncoding(),
            new IsRegexEncoding(),
        ))($value);
    }
}
