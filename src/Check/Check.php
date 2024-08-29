<?php

declare(strict_types=1);

namespace Vivarium\Check;

use ReflectionClass;
use Vivarium\Assertion\Assertion;
use Vivarium\Assertion\Type\IsNamespace;
use Vivarium\Check\Exception\NoSuchMethod;
use Vivarium\Check\Exception\TooFewArguments;
use Vivarium\Check\Exception\TooMuchArguments;

use function array_slice;
use function class_exists;
use function count;
use function ucfirst;

final class Check
{
    private function __construct(private string $namespace)
    {
        (new IsNamespace())
            ->assert($namespace);
    }

    public static function boolean(): Check
    {
        return new Check('Vivarium\Assertion\Boolean');
    }

    public static function comparison(): Check
    {
        return new Check('Vivarium\Assertion\Comparison');
    }

    public static function encoding(): Check
    {
        return new Check('Vivarium\Assertion\Encoding');
    }

    public static function numeric(): Check
    {
        return new Check('Vivarium\Assertion\Numeric');
    }

    public static function object(): Check
    {
        return new Check('Vivarium\Assertion\Object');
    }

    public static function string(): Check
    {
        return new Check('Vivarium\Assertion\String');
    }

    public static function type(): Check
    {
        return new Check('Vivarium\Assertion\Type');
    }

    public static function var(): Check
    {
        return new Check('Vivarium\Assertion\Var');
    }

    /** @param array<mixed> $arguments */
    public function __call(string $name, array $arguments): bool
    {
        /** @var class-string<Assertion<mixed>> $assertion */
        $assertion = $this->namespace . '\\' . ucfirst($name);
        if (! class_exists($assertion)) {
            throw new NoSuchMethod($name, $assertion);
        }

        $reflector = new ReflectionClass($assertion);

        $check       = $reflector->getMethod('__invoke');
        $constructor = $reflector->getConstructor();

        $checkParametersCount       = count($check->getParameters());
        $constructorParametersCount = $constructor === null ? 0 : count($constructor->getParameters());

        $parametersCount = $checkParametersCount + $constructorParametersCount;
        $argumentsCount  = count($arguments);

        if ($parametersCount > $argumentsCount) {
            throw new TooFewArguments($parametersCount, $argumentsCount);
        }

        if ($parametersCount < $argumentsCount) {
            throw new TooMuchArguments($parametersCount, $argumentsCount);
        }

        $checkArguments       = array_slice($arguments, 0, $checkParametersCount);
        $constructorArguments = array_slice($arguments, $checkParametersCount);

        $fn = new $assertion(...$constructorArguments);

        return $fn(...$checkArguments);
    }
}
