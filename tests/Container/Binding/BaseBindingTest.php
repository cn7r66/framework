<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2024 Luca Cantoreggi
 */

namespace Vivarium\Test\Container\Binding;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding;
use Vivarium\Container\Binding\BaseBinding;
use Vivarium\Test\Container\Stub\SimpleStub;

use function assert;

/** @coversDefaultClass \Vivarium\Container\Binding\BaseBinding */
final class BaseBindingTest extends TestCase
{
    /**
     * @covers ::hierarchy()
     * @covers ::expand()
     */
    public function testHierarchy(): void
    {
        $binding = $this->getMockBuilder(BaseBinding::class)
                        ->setConstructorArgs([
                            'integer',
                            'myInt',
                            SimpleStub::class,
                        ])
                        ->onlyMethods([])
                        ->getMock();
        assert($binding instanceof Binding);

        $hierarchy = $binding->hierarchy();

        static::assertCount(7, $hierarchy);
    }
}
