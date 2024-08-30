<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\String;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Encoding\IsSystemEncoding;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Type\Type;

use function mb_strlen;
use function sprintf;

/** @template-implements Assertion<string> */
final class IsLongBetween implements Assertion
{
    public function __construct(private int $min, private int $max, private string $encoding = 'UTF-8')
    {
        (new IsSystemEncoding())
            ->assert($encoding);
    }

    /** @psalm-assert string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected string to be long between %3$s and %4$s. Got %2$s.',
                Type::toLiteral($value),
                Type::toLiteral(mb_strlen($value)),
                Type::toLiteral($this->min),
                Type::toLiteral($this->max),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert string $value */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        $length = mb_strlen($value, $this->encoding);

        return ($this->min <= $length) && ($length <= $this->max);
    }
}
