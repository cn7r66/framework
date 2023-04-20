<?php declare(strict_types=1);

namespace Vivarium\Container\Installer;

use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Container\Key;

final class ContextBinder
{
    public function __construct(
        private Installer $installer,
        private Key $key
    ) {}

    public function global(): TagBinder
    {
        return new TagBinder(
            $this->installer,
            $this->key
        );
    }

    public function for(string $class): TagBinder
    {
        (new IsClassOrInterface())
            ->assert($class);

        return new TagBinder(
            $this->installer,
            new Key(
                $this->key->getType(),
                $class
            )
        );
    }
}