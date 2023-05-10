<?php

declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;
use Vivarium\Container\Provider\ContainerCall;
use Vivarium\Container\Solver\DirectStep;

final class ConcreteTagBinder
{
    public function __construct(
        private Installer $installer,
        private Key $source,
        private Key $target,
    ) {
    }

    public function withTag(string $tag): ScopeBinder
    {
        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->source, function () use ($tag) {
                        return new ContainerCall(
                            new Key(
                                $this->target->getType(),
                                $this->target->getContext(),
                                $tag,
                            ),
                        );
                    }),
            ),
            $this->source,
        );
    }

    public function withoutTag(): ScopeBinder
    {
        return new ScopeBinder(
            $this->installer->withStep(
                $this->installer
                    ->getStep(DirectStep::class)
                    ->withSolver($this->source, function () {
                        return new ContainerCall($this->target);
                    }),
            ),
            $this->source,
        );
    }
}
