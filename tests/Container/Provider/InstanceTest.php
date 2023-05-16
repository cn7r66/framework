<?php

declare(strict_types=1);

namespace Vivarium\Test\Container\Provider;

use PHPUnit\Framework\TestCase;
use stdClass;
use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider\Instance;

/** @coversDefaultClass \Vivarium\Container\Provider\Instance */
final class InstanceTest extends TestCase
{
    /**
     * @dataProvider keyInstanceProvider()
     *
     * @covers ::__construct()
     * @covers ::provide()
     * @covers ::getKey
     */
    public function testProvide(Key $key, mixed $instance): void
    {
        $container = static::createMock(Container::class);

        $provider = new Instance(
            $key,
            $instance
        );

        static::assertSame($instance, $provider->provide($container));
        static::assertSame($key, $provider->getKey());
    }

    /**
     * @return array<array<Key, mixed>>
     */
    public function keyInstanceProvider(): array
    {
        return [
            [
                new Key('int'),
                42
            ],
            [
                new Key(stdClass::class),
                new stdClass()
            ]
        ];
    }
}
