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

    public function testGet()
    {
        $container = new ReflectionContainer();
        $container->get(stdClass::class);
    }

    /** @return array<array-key, array{0: scalar, 1: scalar, 2: bool}> */
    public function getContainerIds(): array
    {
        return [
            'Non existent ID' => [
                'theId',
                false
            ],
            'Non registered existing class' => [
                stdClass::class,
                true
            ]
        ];
    }
}
