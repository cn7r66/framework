<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding\InterceptionBinder;
use Vivarium\Container\Interception;
use Vivarium\Container\Interception\MethodInterception;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Reflection\InstanceMethod;
use Vivarium\Test\Container\Stub\ImmutableStub;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Binding\InterceptionBinder */
final class InterceptionBinderTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::withMethod()
     * @covers ::withInterception()
     * @covers ::bindMethodCall()
     */
    public function testWithMethod(): void
    {
        $binder = new InterceptionBinder(Stub::class, static function (Interception $interception): void {
            static::assertInstanceOf(MethodInterception::class, $interception);

            $call = $interception->getMethodCall();

            static::assertSame('setInt', $call->getName());
        });

        $binder->withMethod('setInt');
    }

    /**
     * @covers ::__construct()
     * @covers ::withImmutableMethod()
     * @covers ::withInterception()
     * @covers ::bindMethodCall()
     */
    public function testWithImmutableMethod(): void
    {
        $binder = new InterceptionBinder(ImmutableStub::class, static function (Interception $interception): void {
            static::assertInstanceOf(MethodInterception::class, $interception);

            $call = $interception->getMethodCall();

            static::assertSame('withInt', $call->getName());
        });

        $binder->withImmutableMethod('withInt');
    }

    /**
     * @covers ::__construct()
     * @covers ::withImmutableMethod()
     * @covers ::withInterception()
     * @covers ::bindMethodCall()
     */
    public function testWithMethodWithConfiguredArguments(): void
    {
        $binder = new InterceptionBinder(ImmutableStub::class, static function (Interception $interception): void {
            static::assertInstanceOf(MethodInterception::class, $interception);

            $call = $interception->getMethodCall();

            $arguments = $call->getArguments();

            static::assertCount(1, $arguments);

            $provider = $arguments->getAtIndex(0);

            static::assertInstanceOf(Instance::class, $provider);
        });

        $binder->withImmutableMethod('withInt', static function (InstanceMethod $method): InstanceMethod {
            return $method->bindParameter('n')
                          ->toInstance(42);
        });
    }
}
