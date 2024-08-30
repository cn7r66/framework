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
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Type\Type;

use function sprintf;

/**
 * @template A
 * @template B
 * @template-implements Assertion<A&B>
 */
final class All implements Assertion
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

    /** @psalm-assert A&B $value */
    public function assert(mixed $value, string $message = ''): void
    {
        try {
            $this->assertion1
                ->assert($value);

            $this->assertion2
                ->assert($value);
        } catch (AssertionFailed $ex) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : $ex->getMessage(),
                Type::toLiteral($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true A&B $value */
    public function __invoke(mixed $value): bool
    {
        return ($this->assertion1)($value) &&
               ($this->assertion2)($value);
    }
}
