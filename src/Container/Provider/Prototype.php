<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Provider;

use Vivarium\Collection\Map\HashMap;
use Vivarium\Collection\Map\Map;
use Vivarium\Container\Binder;
use Vivarium\Container\Container;
use Vivarium\Container\GenericBinder;
use Vivarium\Container\Injectable;
use Vivarium\Container\Injection;
use Vivarium\Container\Provider;
use Vivarium\Container\Reflection\Constructor;
use Vivarium\Container\Reflection\StaticMethod;

use function is_string;

final class Prototype implements Provider
{
    private StaticMethod $constructor;

    /** @var Map<string, Provider> */
    private Map $properties;

    public function __construct(StaticMethod|string $constructor)
    {
        $this->constructor = is_string($constructor) ?
            new Constructor($constructor) : $constructor;

        $this->properties = new HashMap();
    }

    public function provide(Container $container, string|null $requester = null): mixed
    {
        return $this->constructor->invoke($container);
    }

    public function getConstructor(): StaticMethod
    {
        return $this->constructor;
    }

    /** @return Binder<Prototype> */
    public function bindParameter(string $parameter): Binder
    {
        return new GenericBinder(function (Provider $provider) use ($parameter) {
            $prototype              = clone $this;
            $prototype->constructor = $this->constructor
                ->bindParameter($parameter)
                ->toProvider($provider);

            return $prototype;
        });
    }

    public function bindProperty(string $property): Binder
    {
        return new GenericBinder(function (Provider $provider) use ($property) {
            $prototype             = clone $this;
            $prototype->properties = $prototype->properties->put($property, $provider);

            return $prototype;
        });
    }
}
