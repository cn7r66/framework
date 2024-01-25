<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Binding\ClassBinding;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Equality\Equal;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;
use Vivarium\Test\Container\Stub\Stub;

/** @coversDefaultClass \Vivarium\Container\Binding\ClassBinding */
final class ClassBindingTest extends TestCase
{
    /** 
     * @covers ::__construct()
     * @covers ::fromBinding()
     */
    public function testFromBinding(): void
    {
        $binding      = new SimpleBinding(ConcreteStub::class);
        $classBinding = ClassBinding::fromBinding($binding);

        static::assertTrue(Equal::areEquals($classBinding, $binding));
        static::assertInstanceOf(ClassBinding::class, $classBinding);
    }

    /**
     * @covers ::__construct()
     * @covers ::fromBinding()
     */
    public function testFromBindingSameInstance(): void
    {
        $binding = new ClassBinding(ConcreteStub::class);

        $classBinding = ClassBinding::fromBinding($binding);

        static::assertSame($binding, $classBinding);
    }

    /**
     * @covers ::fromBinding()
     */
    public function testFromBindingException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be class or interface name. Got "theId".');

        ClassBinding::fromBinding(
            new SimpleBinding('theId')
        );
    }

    /**
     * @covers ::hierarchy()
     * @covers ::extends()
     * @covers ::interfaces()
     */
    public function testHierarchyNoExtends(): void
    {
        $binding = new ClassBinding(Stub::class);

        $hierarchy = $binding->hierarchy();

        static::assertCount(1, $hierarchy);
        static::assertSame($binding, $hierarchy->getAtIndex(0));
    }

    /**
     * @covers ::hierarchy()
     * @covers ::extends()
     * @covers ::interfaces()
     */
    public function testHierarchy(): void
    {
        $binding = new ClassBinding(
            ConcreteStub::class,
            'theTag',
            SimpleStub::class
        );

        $hierarchy = $binding->hierarchy();

        static::assertCount(19, $hierarchy);
    }

    /** @covers ::getId() */
    public function testGetId(): void
    {
        $binding = new ClassBinding(ConcreteStub::class);

        static::assertTrue(class_exists($binding->getId()));
        static::assertSame(ConcreteStub::class, $binding->getId());
    }
}
