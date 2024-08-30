<?php

declare(strict_types=1);

namespace Vivarium\Assertion\Type;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Var\IsString;
use Vivarium\Type\Type;

use function explode;
use function preg_match;
use function sprintf;

/** @template-implements Assertion<non-empty-string> */
final class IsNamespace implements Assertion
{
    /** @psalm-assert non-empty-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected string to be namespace. Got %s.',
                Type::toLiteral($value),
            );

            throw new AssertionFailed($message);
        }
    }

    /**
     * @psalm-assert string $value
     * @psalm-assert-if-true non-empty-string $value
     */
    public function __invoke(mixed $value): bool
    {
        (new IsString())
           ->assert($value);

        $parts = explode('\\', $value);

        foreach ($parts as $part) {
            if (preg_match('/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/', $part) !== 1) {
                return false;
            }
        }

        return true;
    }
}
