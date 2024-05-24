<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\MethodBinder;
use Vivarium\Container\Reflection\CreationalMethod;
use Vivarium\Container\Reflection\FactoryMethodCall;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\StubFactory;

/** @coversDefaultClass \Vivarium\Container\Binding\MethodBinder */
final class MethodBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::method()
     */
    public function testMethod(): void
    {
        $binder = new MethodBinder(static function (string $method, callable $configure): void {
            $factoryMethod = $configure(new FactoryMethodCall(StubFactory::class, $method));

            static::assertSame('create', $factoryMethod->getName());
            static::assertFalse($factoryMethod->hasParameter('stub'));
        });

        $binder->method('create');
    }

    /**
     * @covers ::__construct()
     * @covers ::method()
     */
    public function testMethodWithConfigure(): void
    {
        $binder = new MethodBinder(static function (string $method, callable $configure): void {
            $factoryMethod = $configure(new FactoryMethodCall(StubFactory::class, $method));

            static::assertSame('create', $factoryMethod->getName());
            static::assertTrue($factoryMethod->hasParameter('stub'));
        });

        $binder->method('create', static function (CreationalMethod $method) {
            return $method->bindParameter('stub')
                            ->to(ConcreteStub::class);
        });
    }
}
