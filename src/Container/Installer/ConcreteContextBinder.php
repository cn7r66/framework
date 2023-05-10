<?php declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Container\Key;

final class ConcreteContextBinder
{
    public function __construct(
        private Installer $installer,
        private Key $source,
        private Key $target
    ) {}

    public function sameContext(): ConcreteTagBinder
    {
        return new ConcreteTagBinder(
            $this->installer,
            $this->source,
            new Key(
                $this->target->getType(),
                $this->source->getContext()
            )
        );
    }

    public function global(): ConcreteTagBinder
    {
        return new ConcreteTagBinder(
            $this->installer,
            $this->source,
            $this->target
        );
    }
}
