<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Test\Container\Installer;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Assertion\Exception\AssertionFailed;
use Vivarium\Container\Exception\StepNotFound;
use Vivarium\Container\Installer\Installer;
use Vivarium\Container\Solver\SolverStep;
use Vivarium\Test\Container\Stub\StubInterface;

use function assert;
use function is_callable;

/** @coversDefaultClass \Vivarium\Container\Installer\Installer */
final class InstallerTest extends TestCase
{
    /** @covers ::withStep() */
    public function testWithStep(): void
    {
        $step = static::createMock(SolverStep::class);

        $installer = (new Installer())
            ->withStep($step, 10);

        static::assertSame($step, $installer->getStep($step::class));
    }

    /** @covers ::withStep() */
    public function testWithStepException(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Must set priority for newly added steps.');

        $step = static::createMock(SolverStep::class);

        (new Installer())
            ->withStep($step);
    }

    /** @covers ::withStepFactory() */
    public function testWithStepFactory(): void
    {
        $step = static::createMock(SolverStep::class);

        $installer = (new Installer())
            ->withStepFactory(
                $step::class,
                static function () use ($step) {
                    return $step;
                },
                10,
            );

        static::assertSame(
            $step,
            $installer->getStep($step::class),
        );
    }

    /** @covers ::getStep() */
    public function testGetStepCallingFactory(): void
    {
        $step = static::createMock(SolverStep::class);

        $shouldBeCalled = $this->getMockBuilder(stdClass::class)
                               ->addMethods(['__invoke'])
                               ->getMock();

        $shouldBeCalled->expects(static::once())
                       ->method('__invoke')
                       ->willReturn($step);

        assert(is_callable($shouldBeCalled));

        $installer = (new Installer())
            ->withStepFactory($step::class, $shouldBeCalled, 10);

        static::assertSame($step, $installer->getStep($step::class));

        $installer = $installer->withStep($step);

        static::assertSame($step, $installer->getStep($step::class));
    }

    /** @covers ::getStep() */
    public function testGetStepWithWrongClass(): void
    {
        static::expectException(AssertionFailed::class);
        static::expectExceptionMessage('Class must be of type "Vivarium\Container\Solver\SolverStep".');

        (new Installer())
            ->getStep(StubInterface::class);
    }

    /** @covers ::getStep() */
    public function testGetStepWithNonRegisteredStep(): void
    {
        static::expectException(StepNotFound::class);
        static::expectExceptionMessage('');

        $step = static::createMock(SolverStep::class);

        (new Installer())
            ->getStep($step::class);
    }

    /** @covers ::getSteps() */
    public function testGetSteps(): void
    {
        $step1 = static::getMockBuilder(SolverStep::class)
                       ->setMockClassName('SolverStep1')
                       ->getMock();

        $step2 = static::getMockBuilder(SolverStep::class)
                       ->setMockClassName('SolverStep2')
                       ->getMock();

        $installer = (new Installer())
            ->withStep($step2, 10)
            ->withStep($step1, 1);

        $expected = [$step1, $step2];

        static::assertSame($expected, $installer->getSteps());
    }
}
