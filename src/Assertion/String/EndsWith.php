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
use function strcmp;
use function strlen;
use function substr;

/** @template-implements Assertion<string> */
final class EndsWith implements Assertion
{
    public function __construct(private string $end)
    {
    }

    /** @psalm-assert string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected that string %s ends with %2$s.',
                Type::toLiteral($value),
                Type::toLiteral($this->end),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert string $value */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        $stringLength = strlen($value);
        $endLength    = strlen($this->end);
        $startOffset  = $stringLength - $endLength;
        $substr       = substr($value, $startOffset, strlen($value));

        return strcmp($this->end, $substr) === 0;
    }
}
