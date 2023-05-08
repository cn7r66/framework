<?php

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Solver\DirectStep;

final class FactoryTagBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key,
        private Key $factory,
        private string $method,
    ) {
    }

    public function withTag(string $tag): ScopeBinder
    {
        $factory = new Key(
            $this->factory->getType(),
            $this->factory->getContext(),
            $tag,
        );

        $method = $this->method;

        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->factory, static function (Key $key) use ($factory, $method) {
                        return new Factory(
                            $key,
                            $factory,
                            $method,
                        );
                    }),
            ),
            $this->key,
        );
    }

    public function withoutTag(): ScopeBinder
    {
        $factory = $this->factory;
        $method  = $this->method;

        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->factory, static function (Key $key) use ($factory, $method) {
                        return new Factory(
                            $key,
                            $factory,
                            $method,
                        );
                    }),
            ),
            $this->key,
        );
    }
}
