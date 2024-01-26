<?php declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Binding\TypeBinding;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\ConcreteStub;

/** @coversDefaultClass \Vivarium\Container\Binding\TypeBinding */
final class TypeBindingTest extends TestCase
{
    /** 
     * @covers ::__construct() 
     * @dataProvider getIds()
     */
    public function testConstruct(string $id): void
    {
        $binding = new TypeBinding($id);

        static::assertSame($id, $binding->getId());
    }

    /** @covers ::__construct() */
    public function testConstructException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Expected string to be a primitive, class, interface, union or intersection. Got "theId".');

        $binding = new TypeBinding('theId');
    }

    public function getIds(): array
    {
        return [
            'Integer' => [
                'int'
            ],
            'Float' => [
                'float'
            ],
            'String' => [
                'string'
            ],
            'Array' => [
                'array'
            ],
            'Callable' => [
                'callable'
            ],
            'Object' => [
                'object'
            ],
            'Boolean' => [
                'bool'
            ],
            'Class' => [
                ConcreteStub::class
            ],
            'Interface' => [
                Stub::class
            ],
            'Intersection' => [
                'Vivarium\Test\Container\Stub\Stub&Stringable'
            ],
            'Union' => [
                'string|Stringable'
            ]
        ];
    }
}
