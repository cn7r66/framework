<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container;

use ReflectionClass;
use ReflectionFunction;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use Vivarium\Assertion\Numeric\IsInHalfOpenRightRange;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Assertion\Var\IsFunction;
use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;

final class Signature
{
    private Map $lookup;

    private Map $parameters;

    private Map $arguments;

    private function __construct()
    {
        $this->lookup     = new HashMap();
        $this->parameters = new HashMap();
        $this->arguments  = new HashMap();
    }

    public static function ofMethod(string $class, string $method): Signature
    {
        (new HasMethod($method))
            ->assert($class);

        return static::parameters(
            new ReflectionMethod($class, $method)
        );
    }

    public static function ofConstructor(string $class) : Signature
    {
        (new IsIstantiable())
            ->assert($class);

        $reflector = (new ReflectionClass($class))
            ->getConstructor();

        if ($reflector === null) {
            return new Signature();
        }

        return static::parameters($reflector);
    }

    public static function ofFunction(callable $fn)
    {
        (new IsFunction())
            ->assert($fn);

        return static::parameters(
            new ReflectionFunction($fn)
        );
    }

    private static function parameters(ReflectionFunctionAbstract $reflector) : Signature
    {

    }

    public function bindArgument(int $position, Provider $provider)
    {
        (new IsInHalfOpenRightRange(0, $this->parameters->count()))
            ->assert($position);
    }

    public function bindArgumentNamed(string $name, Provider $provider)
    {

    }

    public function getArguments()
    {

    }

    public function project(Signature $signature) : Signature
    {

    }
}

