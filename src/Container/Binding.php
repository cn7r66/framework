<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Equality\Equality;

interface Binding extends Equality
{
    public const GLOBAL = '$GLOBAL';

    public const DEFAULT = '$DEFAULT';

    public function getId(): string;

    public function getTag(): string;

    public function getContext(): string;

    public function hierarchy(): Sequence;

    public function widen(): Binding;

    public function couldBeWidened(): bool;
}
