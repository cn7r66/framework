<?php declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use Vivarium\Container\Container;
use Vivarium\Container\Provider;
use Vivarium\Container\Provider\Cloneable;
use Vivarium\Test\Container\Stub\StubInterface;

final class CloneableTest extends TestCase
{
    public function testProvide(): void
    {
        $container = static::createMock(Container::class);
        $cloned    = static::createMock(StubInterface::class);

        $instance = static::getMockBuilder(StubInterface::class)
                          ->addMethods(['__clone'])
                          ->getMock();

        $instance->expects(static::exactly(2))
                 ->method('__clone')
                 ->willReturn($cloned);

        $provider  = static::createMock(Provider::class);

        $provider->expects(static::once())
                 ->method('provide')
                 ->with($container)
                 ->willReturn($instance);

        $cloneable = new Cloneable($provider);

        $a = $cloneable->provide($container);

        static::assertSame($cloned, $a);
        static::assertSame($cloned, $cloneable->provide($container));
    }
}