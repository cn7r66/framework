<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsEmpty;

use function sprintf;

/** @template-implements Assertion<'int'|'float'|'string'|'array'|'callable'|'object'|class-string> */
final class IsBasicType implements Assertion
{
    /** @psalm-assert 'int'|'float'|'string'|'array'|'callable'|'object'|class-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected string to be a primitive type, class or interface. Got %s.',
                (new TypeToString())($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true 'int'|'float'|'string'|'array'|'callable'|'object'|class-string $value
     */
    public function __invoke(mixed $value): bool
    {
        return (new Either(
            new IsPrimitive(),
            new IsClassOrInterface(),
        ))($value);
    }
}
