<?php declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class StubFactory
{
    public function create(): StubImpl
    {
        return new StubImpl();
    }
}