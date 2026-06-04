<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\ProviderBinder;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\ClassConstant;
use Vivarium\Container\Provider\Constant;
use Vivarium\Container\Provider\Constructor;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Provider\Enum;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Provider\StaticFactory;
use Vivarium\Container\Scope;
use Vivarium\Test\Container\Stub\StubClassWithConstants;
use Vivarium\Test\Container\Stub\StubFactory;
use Vivarium\Test\Container\Stub\StubStaticFactory;
use Vivarium\Test\Container\Stub\StubWithConstructor;
use Vivarium\Test\Container\Stub\StubWithNoArgs;

/** @coversDefaultClass \Vivarium\Container\Binding\ProviderBinder */
final class ProviderBinderTest extends TestCase
{
    private function makeBinder(string $type): ProviderBinder
    {
        return new ProviderBinder(
            new Binding($type),
            static function (Binding $binding, Provider $provider): Provider {
                return $provider;
            },
        );
    }

    /** @covers ::toConstructor */
    public function testToConstructorCreatesConstructorProvider(): void
    {
        $provider = $this->makeBinder(StubWithNoArgs::class)->toConstructor();

        static::assertInstanceOf(Constructor::class, $provider);
    }

    /** @covers ::toFactory */
    public function testToFactoryCreatesFactoryProvider(): void
    {
        $provider = $this->makeBinder(StubWithConstructor::class)
            ->toFactory(StubFactory::class)
            ->method('create');

        static::assertInstanceOf(Factory::class, $provider);
    }

    /** @covers ::toStaticFactory */
    public function testToStaticFactoryCreatesStaticFactoryProvider(): void
    {
        $provider = $this->makeBinder(StubWithConstructor::class)
            ->toStaticFactory(StubStaticFactory::class)
            ->method('create');

        static::assertInstanceOf(StaticFactory::class, $provider);
    }

    /** @covers ::toInstance */
    public function testToInstanceCreatesInstanceProvider(): void
    {
        $instance = new StubWithNoArgs();
        $provider = $this->makeBinder(StubWithNoArgs::class)->toInstance($instance);

        static::assertInstanceOf(Instance::class, $provider);
    }

    /** @covers ::toConstant */
    public function testToConstantCreatesConstantProvider(): void
    {
        $provider = $this->makeBinder('int')->toConstant('PHP_INT_MAX');

        static::assertInstanceOf(Constant::class, $provider);
    }

    /** @covers ::toEnum */
    public function testToEnumCreatesEnumProvider(): void
    {
        $provider = $this->makeBinder(Scope::class)->toEnum(Scope::class, 'SERVICE');

        static::assertInstanceOf(Enum::class, $provider);
    }

    /** @covers ::toClassConstant */
    public function testToClassConstantCreatesClassConstantProvider(): void
    {
        $provider = $this->makeBinder('int')
            ->toClassConstant(StubClassWithConstants::class, 'INT_CONSTANT');

        static::assertInstanceOf(ClassConstant::class, $provider);
    }

    /** @covers ::to */
    public function testToCreatesContainerCallProvider(): void
    {
        $provider = $this->makeBinder(StubWithNoArgs::class)
            ->to(StubWithNoArgs::class);

        static::assertInstanceOf(ContainerCall::class, $provider);
    }
}
