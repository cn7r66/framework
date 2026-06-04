<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Capability;
use Vivarium\Container\Container;
use Vivarium\Container\Exception\ParameterNotFound;
use Vivarium\Container\Exception\ParameterNotSolvable;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Constructor;
use Vivarium\Test\Container\Stub\NotInstantiableStub;
use Vivarium\Test\Container\Stub\StubClass;
use Vivarium\Test\Container\Stub\StubService;
use Vivarium\Test\Container\Stub\StubWithNoArgs;
use Vivarium\Test\Container\Stub\StubWithNoConstructor;
use Vivarium\Test\Container\Stub\StubWithOptionalArg;
use Vivarium\Test\Container\Stub\StubWithUntypedOptionalArg;
use Vivarium\Test\Container\Stub\StubWithUntypedRequiredArg;

/** @coversDefaultClass \Vivarium\Container\Provider\Constructor */
final class ConstructorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::__construct
     * @covers \Vivarium\Container\BaseMethod::getArguments
     * @covers \Vivarium\Container\BaseMethod::getArgumentsValue
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvideCreatesInstanceWithDependency(): void
    {
        $service   = new StubService();
        $container = $this->createMock(Container::class);
        $container->method('get')->willReturn($service);

        $result = (new Constructor(StubClass::class))->provide($container);

        static::assertInstanceOf(StubClass::class, $result);
        static::assertSame($service, $result->service);
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::getArguments
     * @covers \Vivarium\Container\BaseMethod::getArgumentsValue
     */
    public function testProvideCreatesInstanceWithNoArgs(): void
    {
        $container = $this->createMock(Container::class);
        $container->expects(static::never())->method('get');

        $result = (new Constructor(StubWithNoArgs::class))->provide($container);

        static::assertInstanceOf(StubWithNoArgs::class, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvideUsesDefaultWhenOptionalArgNotInContainer(): void
    {
        $container = $this->createMock(Container::class);
        $container->method('has')->willReturn(false);

        $result = (new Constructor(StubWithOptionalArg::class))->provide($container);

        static::assertInstanceOf(StubWithOptionalArg::class, $result);
        static::assertSame('default', $result->name);
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvideUsesContainerValueForOptionalArg(): void
    {
        $container = $this->createMock(Container::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturn('custom');

        $result = (new Constructor(StubWithOptionalArg::class))->provide($container);

        static::assertSame('custom', $result->name);
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvideWithUntypedOptionalArgUsesDefault(): void
    {
        $container = $this->createMock(Container::class);
        $container->expects(static::never())->method('get');

        $result = (new Constructor(StubWithUntypedOptionalArg::class))->provide($container);

        static::assertSame('default', $result->getValue());
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvideThrowsForUntypedRequiredArg(): void
    {
        static::expectException(ParameterNotSolvable::class);

        $container = $this->createMock(Container::class);
        (new Constructor(StubWithUntypedRequiredArg::class))->provide($container);
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers \Vivarium\Container\BaseMethod::solveParameter
     */
    public function testProvidePrefersExplicitlyBoundArgumentOverAutoResolution(): void
    {
        $service = new StubService();
        $custom  = $this->createMock(Provider::class);
        $custom->method('provide')->willReturn($service);

        $container = $this->createMock(Container::class);
        $container->expects(static::never())->method('get');

        $result = (new Constructor(StubClass::class))
            ->bindArgument('service')
            ->toProvider($custom)
            ->provide($container);

        static::assertInstanceOf(StubClass::class, $result);
        static::assertSame($service, $result->service);
    }

    /** @covers ::getTarget */
    public function testGetTargetReturnsClassName(): void
    {
        static::assertSame(
            StubClass::class,
            (new Constructor(StubClass::class))->getTarget(),
        );
    }

    /** @covers ::getCapabilities */
    public function testGetCapabilitiesIncludesAll(): void
    {
        $capabilities = (new Constructor(StubClass::class))->getCapabilities();

        static::assertTrue($capabilities->contains(Capability::INJECTABLE));
        static::assertTrue($capabilities->contains(Capability::INTERCEPTABLE));
        static::assertTrue($capabilities->contains(Capability::DECORABLE));
        static::assertCount(3, $capabilities);
    }

    /**
     * @covers ::__construct
     * @covers ::getArguments
     * @covers \Vivarium\Container\BaseMethod::getArguments
     */
    public function testGetArgumentsReturnsEmptyForNoArgsConstructor(): void
    {
        static::assertCount(
            0,
            (new Constructor(StubWithNoArgs::class))->getArguments(),
        );
    }

    /** @covers \Vivarium\Container\BaseMethod::getClass */
    public function testGetClassReturnsClassName(): void
    {
        static::assertSame(
            StubClass::class,
            (new Constructor(StubClass::class))->getClass(),
        );
    }

    /** @covers \Vivarium\Container\BaseMethod::getName */
    public function testGetNameReturnsMethodName(): void
    {
        static::assertSame(
            '__construct',
            (new Constructor(StubClass::class))->getName(),
        );
    }

    /**
     * @covers \Vivarium\Container\BaseMethod::bindArgument
     * @covers \Vivarium\Container\BaseMethod::hasArgument
     * @covers \Vivarium\Container\BaseMethod::getArgument
     */
    public function testBindArgumentOverridesParameterProvider(): void
    {
        $custom   = $this->createMock(Provider::class);
        $modified = (new Constructor(StubClass::class))
            ->bindArgument('service')
            ->toProvider($custom);

        static::assertTrue($modified->hasArgument('service'));
        static::assertSame($custom, $modified->getArgument('service'));
    }

    /** @covers \Vivarium\Container\BaseMethod::bindArgumentAtPosition */
    public function testBindArgumentAtPositionOverridesParameterProvider(): void
    {
        $custom   = $this->createMock(Provider::class);
        $modified = (new Constructor(StubClass::class))
            ->bindArgumentAtPosition(0)
            ->toProvider($custom);

        static::assertTrue($modified->hasArgument('service'));
        static::assertSame($custom, $modified->getArgument('service'));
    }

    /**
     * @covers ::__construct
     * @covers ::provide
     * @covers ::getArguments
     * @covers \Vivarium\Container\BaseMethod::getArguments
     */
    public function testProvideCreatesInstanceWithNoExplicitConstructor(): void
    {
        $container = $this->createMock(Container::class);
        $container->expects(static::never())->method('get');

        $result = (new Constructor(StubWithNoConstructor::class))->provide($container);

        static::assertInstanceOf(StubWithNoConstructor::class, $result);
    }

    /** @covers ::__construct */
    public function testThrowsForNotInstantiableClass(): void
    {
        static::expectException(AssertionFailed::class);

        new Constructor(NotInstantiableStub::class);
    }

    /** @covers \Vivarium\Container\BaseMethod::bindArgument */
    public function testBindArgumentAcceptsUntypedParameter(): void
    {
        $custom   = $this->createMock(Provider::class);
        $modified = (new Constructor(StubWithUntypedOptionalArg::class))
            ->bindArgument('value')
            ->toProvider($custom);

        static::assertSame($custom, $modified->getArgument('value'));
    }

    /** @covers \Vivarium\Container\BaseMethod::bindArgument */
    public function testBindArgumentThrowsForUnknownParameterName(): void
    {
        static::expectException(ParameterNotFound::class);

        (new Constructor(StubClass::class))->bindArgument('nonExistent');
    }

    /** @covers \Vivarium\Container\BaseMethod::getArgument */
    public function testGetArgumentThrowsWhenNotBound(): void
    {
        static::expectException(ParameterNotFound::class);

        (new Constructor(StubClass::class))->getArgument('service');
    }

    /** @covers \Vivarium\Container\BaseMethod::equals */
    public function testEqualsReturnsTrueForSameClassAndMethod(): void
    {
        $a = new Constructor(StubClass::class);
        $b = new Constructor(StubClass::class);

        static::assertTrue($a->equals($b));
        static::assertTrue($a->equals($a));
    }

    /** @covers \Vivarium\Container\BaseMethod::equals */
    public function testEqualsReturnsFalseForDifferentClass(): void
    {
        $a = new Constructor(StubClass::class);
        $b = new Constructor(StubWithNoArgs::class);

        static::assertFalse($a->equals($b));
    }

    /** @covers \Vivarium\Container\BaseMethod::equals */
    public function testEqualsReturnsFalseForNonMethod(): void
    {
        static::assertFalse(
            (new Constructor(StubClass::class))->equals(new stdClass()),
        );
    }

    /** @covers \Vivarium\Container\BaseMethod::hash */
    public function testHashIsConsistentForSameInstance(): void
    {
        $a = new Constructor(StubClass::class);

        static::assertSame($a->hash(), $a->hash());
    }

    /** @covers \Vivarium\Container\BaseMethod::hash */
    public function testHashDiffersForDifferentClasses(): void
    {
        $a = new Constructor(StubClass::class);
        $b = new Constructor(StubWithNoArgs::class);

        static::assertNotSame($a->hash(), $b->hash());
    }
}
