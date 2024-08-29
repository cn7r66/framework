<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Object;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Type\IsClass;
use Vivarium\Assertion\Type\IsInterface;
use Vivarium\Assertion\Var\IsObject;

use function sprintf;

/**
 * @template T as object
 * @template-implements Assertion<T>
 */
final class IsInstanceOf implements Assertion
{
    /**
     * @param class-string<T> $class
     *
     * @throws AssertionFailed
     */
    public function __construct(private string $class)
    {
        (new Either(
            new IsClass(),
            new IsInterface(),
        ))->assert($class, 'Argument must be a class or interface name. Got %s');
    }

    /** @psalm-assert T $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected object to be instance of %2$s. Got %s.',
                (new TypeToString())($value),
                (new TypeToString())($this->class),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true T $value */
    public function __invoke(mixed $value): bool
    {
        (new IsObject())
            ->assert($value);

        return $value instanceof $this->class;
    }
}
