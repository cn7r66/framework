<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface Solver
{
    /** @param callable(): Provider $next */
    public function solve(Binding $request, callable $next): Provider;
}
