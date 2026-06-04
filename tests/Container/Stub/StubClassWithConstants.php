<?php

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class StubClassWithConstants
{
    public const INT_CONSTANT    = 42;
    public const STRING_CONSTANT = 'hello';
    public const FLOAT_CONSTANT  = 3.14;
    public const BOOL_CONSTANT   = true;
    public const NULL_CONSTANT   = null;
    public const ARRAY_CONSTANT  = ['foo', 'bar'];
}
