<?php

declare(strict_types=1);

namespace Vivarium\Check\Exception;

use RuntimeException;

use function sprintf;

final class TooMuchArguments extends RuntimeException
{
    public function __construct(int $expected, int $actual)
    {
        parent::__construct(
            sprintf(
                'Too much arguments provided. Expected %s, got %s',
                $expected,
                $actual,
            ),
        );
    }
}
