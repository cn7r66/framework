<?php declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;
use Vivarium\Container\Solver\ScopeStep;

final class ScopeBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key
    ) {}

    public function prototype(): Binder
    {
        return new Binder(
            $this->installer
        );
    }

    public function service(): Binder
    {
        return new Binder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(ScopeStep::class)
                    ->addService($this->key)
            )
        );
    }

    public function cloneable(): Binder
    {
        return new Binder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(ScopeStep::class)
                    ->addCloneable($this->key)
            )
        );
    }
}