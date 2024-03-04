<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface Interceptable extends Provider
{
    public function withInjection(Interception $injection): self;

    public function withUniqueInjection(Interception $injection): self;
}
