<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\ReflectionContainer;
use Vivarium\Test\Container\Stub\ConcreteStub;
use Vivarium\Test\Container\Stub\SimpleStub;

/** @coversDefaultClass Vivarium\Container\ReflectionContainer */
final class ReflectionContainerTest extends TestCase
{
    /** 
     * @covers ::has 
     * @dataProvider getContainerIds
     */
    public function testHasWithString(string $id, bool $result): void
    {
        $container = new ReflectionContainer();

        static::assertSame($container->has($id), $result);
    }

    /** @covers ::get() */
    public function testGet()
    {
        $container = new ReflectionContainer();
        $instance  = $container->get(stdClass::class);

        static::assertInstanceOf(stdClass::class, $instance);
    }

    /** @return array<array-key, array<string, bool>> */
    public function getContainerIds(): array
    {
        return [
            'Non existent ID' => [
                'theId',
                false
            ],
            'Class without contructor' => [
                stdClass::class,
                true
            ],
            'Class with empty constructor' => [
                ConcreteStub::class,
                true
            ],
            'Class with constructor' => [
                SimpleStub::class,
                true
            ]
        ];
    }
}
