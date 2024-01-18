<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

interface Injectable
{
    public function withInjection(Injection $injection): self;

    public function withUniqueInjection(Injection $injection): self;
}
