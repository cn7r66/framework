<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Var;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\String\IsEmpty;

/**
 * @template T of int|float
 * @template-implements Assertion<T>
 */
final class IsNumeric implements Assertion
{
    /** @var Either<int, float> */
    private Assertion $isNumeric;

    public function __construct()
    {
        $this->isNumeric = new Either(
            new IsInteger(),
            new IsFloat(),
        );
    }

    /** @psalm-assert T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        $this->isNumeric->assert(
            $value,
            ! (new IsEmpty())($message) ?
                $message : 'Expected value to be either integer or float. Got %2$s.',
        );
    }

    /** @psalm-assert-if-true T $value */
    public function __invoke(mixed $value): bool
    {
        return ($this->isNumeric)($value);
    }
}
