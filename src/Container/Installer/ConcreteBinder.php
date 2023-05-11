<?php

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Assertion\Conditional\All;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Assertion\Object\HasMethod;
use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Container\Key;
use Vivarium\Container\Provider\Factory;
use Vivarium\Container\Provider\Instance;
use Vivarium\Container\Solver\DirectStep;

final class ConcreteBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key,
    ) {
    }

    public function to(string $class): ConcreteTagBinder
    {
        (new IsAssignableTo($this->key->getType()))
            ->assert($class);

        return new ConcreteTagBinder(
            $this->installer,
            $this->key,
            new Key($class),
        );
    }

    public function toInstance(mixed $instance): ScopeBinder
    {
        (new IsAssignableTo($this->key->getType()))
            ->assert($instance::class);

        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->key, function () use ($instance) {
                        return new Instance(
                            $this->key,
                            $instance,
                        );
                    }),
            ),
            $this->key,
        );
    }

    public function toFactory(string $factory, string $method): ScopeBinder
    {
        (new HasMethod($method))
            ->assert($factory);

        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->key, static function (Key $key) use ($factory) {
                        return new Factory(
                            $factory,
                            $key,
                        );
                    }),
            ),
            $this->key,
        );
    }
}
