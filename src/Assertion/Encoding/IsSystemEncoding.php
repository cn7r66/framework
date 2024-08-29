<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Assertion\Encoding;

use ValueError;
use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\Var\IsBoolean;
use Vivarium\Assertion\Var\IsString;

use function mb_internal_encoding;
use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsSystemEncoding implements Assertion
{
    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! ($this)($value)) {
            $message = sprintf(
                '%s is not a valid system encoding.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true non-empty-string $value
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
            ->assert($value);

        $encoding = mb_internal_encoding();
        (new IsString())
            ->assert($encoding);

        try {
            $valid = @mb_internal_encoding($value);
            (new IsBoolean())
                ->assert($valid);
        } catch (ValueError) {
            $valid = false;
        } finally {
            // Restore the previous encoding
            mb_internal_encoding($encoding);
        }

        return $valid;
    }
}
