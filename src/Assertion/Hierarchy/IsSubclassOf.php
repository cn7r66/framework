<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Hierarchy;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsClass;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\String\IsInterface;

use function is_subclass_of;
use function sprintf;

/**
 * @template T
 * @template-implements Assertion<class-string>
 * @psalm-immutable
 */
final class IsSubclassOf implements Assertion
{
    /** @var class-string<T> */
    private string $class;

    /** @param class-string<T> $class */
    public function __construct(string $class)
    {
        (new IsClassOrInterface())->assert($class);

        $this->class = $class;
    }

    /**
     * @param class-string $value
     *
     * @psalm-assert class-string<T> $value
     */
    public function assert($value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected class %s to be subclass of %2$s.',
                (new TypeToString())($value),
                (new TypeToString())($this->class),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @param class-string $value
     *
     * @psalm-assert-if-true class-string<T> $value
     */
    public function __invoke($value): bool
    {
        (new Either(
            new IsClass(),
            new IsInterface(),
        ))->assert($value, 'Argument must be a class or interface name. Got %s');

        return is_subclass_of($value, $this->class, true);
    }
}
