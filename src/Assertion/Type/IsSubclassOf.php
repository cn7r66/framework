<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Type\Type;

use function is_subclass_of;
use function sprintf;

/**
 * @template T
 * @template-implements Assertion<class-string<T>>
 */
final class IsSubclassOf implements Assertion
{
    /** @param class-string<T> $class */
    public function __construct(private string $class)
    {
        (new IsClassOrInterface())
            ->assert($class);
    }

    /** @psalm-assert class-string<T> $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected class %s to be subclass of %2$s.',
                Type::toLiteral($value),
                Type::toLiteral($this->class),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert class-string $value
     * @psalm-assert-if-true class-string<T> $value
     */
    public function __invoke(mixed $value): bool
    {
        (new Either(
            new IsClass(),
            new IsInterface(),
        ))->assert($value, 'Expected string to be class or interface name. Got %s');

        return is_subclass_of($value, $this->class, true);
    }
}
