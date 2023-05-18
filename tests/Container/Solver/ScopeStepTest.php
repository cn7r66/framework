<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Solver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Container\Provider\Service;
use Vivarium\Container\Solver\ScopeStep;
use Vivarium\Test\Container\Stub\Stub;
use Vivarium\Test\Container\Stub\StubImpl;

/** @coversDefaultClass \Vivarium\Container\Solver\ScopeStep */
final class ScopeStepTest extends TestCase
{
    /**
     * @covers ::__construct()
     * @covers ::addService()
     * @covers ::solve()
     */
    public function testSolveService(): void
    {
        $providerKey = new Key(StubImpl::class);

        $provider = static::createMock(Provider::class);

        $provider->expects(static::once())
            ->method('getKey')
            ->willReturn($providerKey);

        /** @psalm-var MockObject&callable $next */
        $next = static::getMockBuilder(stdClass::class)
                      ->addMethods(['__invoke'])
                      ->getMock();

        $next->expects(static::once())
             ->method('__invoke')
             ->willReturn($provider);

        $key = new Key(Stub::class);

        $step = (new ScopeStep())
            ->addService($key);

        $service = $step->solve($key, $next);

        static::assertInstanceOf(Service::class, $service);
        static::assertSame($providerKey, $service->getKey());
    }

    /**
     * @covers ::__construct()
     * @covers ::addCloneable()
     * @covers ::solve()
     */
    public function testSolveCloneable(): void
    {
        $providerKey = new Key(StubImpl::class);

        $provider = static::createMock(Provider::class);

        $provider->expects(static::once())
                 ->method('getKey')
                 ->willReturn($providerKey);

        /** @psalm-var MockObject&callable $next */
        $next = static::getMockBuilder(stdClass::class)
                      ->addMethods(['__invoke'])
                      ->getMock();

        $next->expects(static::once())
             ->method('__invoke')
             ->willReturn($provider);

        $key = new Key(Stub::class);

        $step = (new ScopeStep())
            ->addCloneable($key);

        $cloneable = $step->solve($key, $next);

        static::assertInstanceOf(Cloneable::class, $cloneable);
        static::assertSame($providerKey, $cloneable->getKey());
    }

    /**
     * @covers ::__construct()
     * @covers ::solve()
     */
    public function testSolve(): void
    {
        $provider = static::createMock(Provider::class);

        /** @psalm-var MockObject&callable $next */
        $next = static::getMockBuilder(stdClass::class)
                      ->addMethods(['__invoke'])
                      ->getMock();

        $next->expects(static::once())
             ->method('__invoke')
             ->willReturn($provider);

        $key = new Key(Stub::class);

        $provider = (new ScopeStep())
            ->solve($key, $next);

        static::assertSame($provider, $provider);
    }
}
