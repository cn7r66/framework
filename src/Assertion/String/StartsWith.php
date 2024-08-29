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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\Var\IsString;

use function sprintf;
use function strcmp;
use function strlen;
use function substr;

/** @template-implements Assertion<string> */
final class StartsWith implements Assertion
{
    public function __construct(private string $start)
    {
    }

    /** @psalm-assert string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected that string %s starts with %2$s.',
                (new TypeToString())($value),
                (new TypeToString())($this->start),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert string $value */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        $startLength = strlen($this->start);
        $substr      = substr($value, 0, $startLength);

        return strcmp($this->start, $substr) === 0;
    }
}
