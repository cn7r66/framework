<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Type\Type;

use function class_exists;
use function sprintf;

/** @template-implements Assertion<class-string> */
final class IsClass implements Assertion
{
    /** @psalm-assert class-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected string to be class name. Got %s.',
                Type::toLiteral($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true class-string $value
     */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        return class_exists($value);
    }
}
