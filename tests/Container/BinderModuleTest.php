<?php declare(strict_types=1);

namespace Vivarium\Test\Container;

use Vivarium\Collection\Collection;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Container\Installer\Binder;
use Vivarium\Container\Installer\BinderModule;
use Vivarium\Container\Installer\Installer;
use Vivarium\Dispatcher\EventDispatcher;

final class BinderModuleTest extends BinderModule
{
    protected function configure(Binder $binder): Installer
    {
        return $binder
            // Collection
            ->bind(Collection::class)
            ->for(EventDispatcher::class)
            ->withoutTag()
            ->to(ArraySequence::class)
            ->withoutTag()
            ->service()

            //Other Class
            ->bind(Foo::class)
            ->global()
            ->withTag('Mega.Foo')
            ->toFactory(FooFactory::class)
            ->cloneable()

            // Flush
            ->getInstaller();
    }
}
