<?php

declare(strict_types=1);

namespace Vivarium\Assertion\Object;

use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Conditional\Either;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Assertion\Helpers\TypeToString;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Assertion\Type\IsObject;

use function method_exists;
use function sprintf;

/** @template-implements Assertion<class-string|object> */
final class HasMethod implements Assertion
{
    public function __construct(private string $method)
    {
    }

    /** @psalm-assert class-string $value */
    public function assert(mixed $value, string $message = ''): void
    {
        if (! $this($value)) {
            $message = sprintf(
                ! (new IsEmpty())($message) ?
                    $message : 'Expected %s to have a method named %2$s.',
                (new TypeToString())($value),
                (new TypeToString())($this->method),
            );

            throw new AssertionFailed($message);
        }
    }

    /** @psalm-assert-if-true class-string $value */
    public function __invoke(mixed $value): bool
    {
        (new Either(
            new IsClassOrInterface(),
            new IsObject(),
        ))->assert($value, 'Value must be either class, interface or object. Got %s');

        return method_exists($value, $this->method) || $this->method === '__construct';
    }
}
