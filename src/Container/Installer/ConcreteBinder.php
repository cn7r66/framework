<?php declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Solver\DirectStep;

final class ConcreteBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key
    ) {}

    public function to(string $class): ConcreteTagBinder
    {
        return new ConcreteTagBinder(
            $this->installer,
            $this->key,
            new Key($class)
        );
    }

    public function toInstance(mixed $instance): ScopeBinder
    {
        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->key, function() use ($instance) {
                        return new Instance($instance);
                    })
            ),
            $this->key
        );
    }

    public function toFactory(string $factory): ScopeBinder
    {
        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->key, function(Key $key) use ($factory) {
                        return new Factory(
                            $factory,
                            $key
                        );
                    })
            ),
            $this->key
        );
    }
}
