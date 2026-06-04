<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Container\Injection;

use ReflectionClass;
use Vivarium\Assertion\Type\IsAssignableTo;
use Vivarium\Container\BaseMethod;
use Vivarium\Container\Container;
use Vivarium\Container\Injection;
use Vivarium\Container\Provider;

use function class_exists;

final class MethodCall extends BaseMethod implements Injection
{
    public function enhance(mixed $instance, Container $container): mixed
    {
        (new IsAssignableTo($this->getClass()))
            ->assert($instance::class);

        (new ReflectionClass($instance::class))
            ->getMethod($this->getName())
            ->invokeArgs(
                $instance,
                $this->getArgumentsValue($container, $instance::class)->toArray(),
            );

        return $instance;
    }

    public function accept(Provider $provider): bool
    {
        $target = $provider->getTarget();

        return class_exists($target)
            && (new ReflectionClass($target))->hasMethod($this->getName());
    }
}
