<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Enhancement;

use ReflectionClass;
use Vivarium\Assertion\Type\IsAssignableTo;
use Vivarium\Container\BaseMethod;
use Vivarium\Container\Container;
use Vivarium\Container\Enhancement;
use Vivarium\Container\Provider;

final class ImmutableMethodCall extends BaseMethod implements Enhancement
{
    public function __construct(string $class, string $method)
    {
        parent::__construct($class, $method);
    }

    public function enhance(mixed $instance, Container $container): mixed
    {
        (new IsAssignableTo($this->getClass()))
            ->assert($instance::class);

        $result = (new ReflectionClass($instance::class))
            ->getMethod($this->getName())
            ->invokeArgs(
                $instance,
                $this->getArgumentsValue($container, $instance::class)->toArray(),
            );

        (new IsAssignableTo($instance::class))
            ->assert($result::class);

        return $result;
    }

    public function accept(Provider $provider): bool
    {
        $target = $provider->getTarget();

        return class_exists($target)
            && (new ReflectionClass($target))->hasMethod($this->getName());
    }
}
