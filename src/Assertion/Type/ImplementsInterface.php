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
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function class_implements;
use function in_array;
use function sprintf;

/**
 * @template T
 * @template-implements Assertion<class-string<T>>
 */
final class ImplementsInterface implements Assertion
{
    /** @param class-string<T> $interface */
    public function __construct(private string $interface)
    {
        (new IsInterface())
            ->assert($interface);
    }

    /** @psalm-assert class-string<T> $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Expected class %s to implements %2$s.',
                (new TypeToString())($value),
                (new TypeToString())($this->interface),
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
        (new IsClass())
            ->assert($value);

        $interfaces = class_implements($value);

        return $interfaces !== false && in_array($this->interface, $interfaces, true);
    }
}
