<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\SimpleBinding;
use Vivarium\Container\Exception\CannotBeWidened;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;

use function sprintf;

/** @coversDefaultClass \Vivarium\Container\Binding\SimpleBinding */
final class SimpleBindingTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::getId()
     * @covers ::getTag()
     * @covers ::getContext()
     */
    public function testBindingCreation(): void
    {
        $binding = new SimpleBinding(ConcreteStub::class);

        static::assertSame(ConcreteStub::class, $binding->getId());
        static::assertSame(Binding::DEFAULT, $binding->getTag());
        static::assertSame(Binding::GLOBAL, $binding->getContext());
    }

    /**
     * @covers ::couldBeWidened()
     * @dataProvider getBindings()
     */
    public function testCouldBeWidened(string $id, string $tag, string $context, bool $expected): void
    {
        $binding = new SimpleBinding($id, $tag, $context);

        static::assertSame($expected, $binding->couldBeWidened());
    }

    /** @covers ::widen() */
    public function testWiden(): void
    {
        $binding = new SimpleBinding(
            ConcreteStub::class,
            'theTag',
            'Vivarium\Test',
        );

        $widen1 = $binding->widen();

        static::assertSame(Binding::DEFAULT, $widen1->getTag());
        static::assertSame('Vivarium\Test', $widen1->getContext());

        $widen2 = $widen1->widen();

        static::assertSame(Binding::DEFAULT, $widen2->getTag());
        static::assertSame('Vivarium', $widen2->getContext());
    }

    /** @covers ::widen() */
    public function testWidenException(): void
    {
        static::expectException(CannotBeWidened::class);
        static::expectExceptionMessage(
            sprintf('Binding with id %s, context $GLOBAL and tag $DEFAULT cannot be widened.', ConcreteStub::class),
        );

        $binding = new SimpleBinding(ConcreteStub::class);

        static::assertFalse($binding->couldBeWidened());

        $binding->widen();
    }

    /** @covers ::equals() */
    public function testEqual(): void
    {
        $binding = new SimpleBinding(
            ConcreteStub::class,
            'theTag',
            SimpleStub::class,
        );

        $binding1 = $this->createMock(Binding::class);
        $binding1->expects(static::once())
                 ->method('getId')
                 ->willReturn(ConcreteStub::class);

        $binding1->expects(static::once())
                 ->method('getTag')
                 ->willReturn('theTag');

        $binding1->expects(static::once())
                 ->method('getContext')
                 ->willReturn(SimpleStub::class);

        static::assertTrue($binding->equals($binding));
        static::assertTrue($binding->equals($binding1));
        static::assertFalse($binding->equals(new stdClass()));
        static::assertFalse($binding->equals(new SimpleBinding(ConcreteStub::class)));
    }

    /** @covers ::hash() */
    public function testHash(): void
    {
        $binding = new SimpleBinding(
            ConcreteStub::class,
            'theTag',
            SimpleStub::class,
        );

        $binding1 = new SimpleBinding(
            ConcreteStub::class,
            'theTag',
            SimpleStub::class,
        );

        $binding2 = new SimpleBinding(ConcreteStub::class);

        static::assertSame($binding->hash(), $binding->hash());
        static::assertSame($binding->hash(), $binding1->hash());
        static::assertNotSame($binding1->hash(), $binding2->hash());
    }

    /** @return array<array<string, string, string, bool>> */
    public function getBindings(): array
    {
        return [
            'Binding with ID' => [
                'theId',
                Binding::DEFAULT,
                Binding::GLOBAL,
                false,
            ],
            'Binding with class' => [
                ConcreteStub::class,
                Binding::DEFAULT,
                Binding::GLOBAL,
                false,
            ],
            'Binding with tag' => [
                ConcreteStub::class,
                'theTag',
                Binding::GLOBAL,
                true,
            ],
            'Binding with context' => [
                ConcreteStub::class,
                Binding::DEFAULT,
                SimpleStub::class,
                true,
            ],
            'Binding with tag and context' => [
                ConcreteStub::class,
                'theTag',
                SimpleStub::class,
                true,
            ],
        ];
    }
}
