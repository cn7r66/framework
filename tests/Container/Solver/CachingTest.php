<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2021 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Binding;
use Vivarium\Container\Provider;
use Vivarium\Container\Solver\Caching;

/** @coversDefaultClass \Vivarium\Container\Solver\Caching */
final class CachingTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::solve()
     */
    public function testSolve(): void
    {
        $binding = $this->createMock(Binding::class);

        $caching = new Caching("/tmp");
        $caching->solve($binding, function (): Provider {
            static::assertTrue(true);

            return $this->createMock(Provider::class);
        });
    }
}
