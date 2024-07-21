<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Definition;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Prototype;
use Vivarium\Container\Scope;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;

/** @coversDefaultClass \Vivarium\Container\Binding\ProviderBinder */
final class ProviderBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::as()
     */
    public function testAs(): void
    {
        $definition = new Prototype(SimpleStub::class);

        $binder = new ProviderBinder($definition, function (Definition $provider) use ($definition) : Definition {
            static::assertNotSame($definition, $provider);

            return $provider;
        });

        $binder->as(function (Definition $definition): Definition {
            return $definition->bindParameter('stub')
                              ->to(ConcreteStub::class);
        });
    }
}
