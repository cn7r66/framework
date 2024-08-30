<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\String;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Type\Type;

use function sprintf;
use function str_contains;

/** @template-implements Assertion<string> */
final class Contains implements Assertion
{
    public function __construct(private string $substring)
    {
    }

    /** @psalm-assert string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected that string contains %2$s.',
                Type::toLiteral($value),
                Type::toLiteral($this->substring),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert string $value */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        return str_contains($value, $this->substring);
    }
}
