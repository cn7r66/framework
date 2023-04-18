<?php declare(strict_types=1);

namespace Vivarium\Container\Provider;

use Vivarium\Container\Container;
use Vivarium\Container\Key;
use Vivarium\Container\Provider;

final class ContainerCall implements Provider
{
    public function __construct(private Key $key) {}

    public function provide(Container $container): mixed
    {
        return $container->get($this->key);
    }

    public function getKey(): Key
    {
        return $this->key;
    }
}