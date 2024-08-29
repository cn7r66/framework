<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Conditional;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function gettype;
use function sprintf;

/**
 * @template A
 * @template B
 * @template-implements Assertion<A|B>
 */
final class Either implements Assertion
{
    /**
     * @param Assertion<A> $assertion1
     * @param Assertion<B> $assertion2
     */
    public function __construct(
        private Assertion $assertion1,
        private Assertion $assertion2,
    ) {
    }

    /** @psalm-assert A|B $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                     $message : 'Failed all assertions in either condition.',
                (new TypeToString())($value),
                gettype($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true A|B $value */
    public function __invoke(mixed $value): bool
    {
        return $this->safeAssert($this->assertion1, $value) ||
               $this->safeAssert($this->assertion2, $value);
    }

    /**
     * @param Assertion<T> $assertion
     *
     * @template T
     */
    public function safeAssert(Assertion $assertion, mixed $value): bool
    {
        try {
            return $assertion($value);
        } catch (AssertionFailed) {
            return false;
        }
    }
}
