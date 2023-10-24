<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

declare(strict_types=1);

namespace Vivarium\Container;

interface Interceptor
{
    public function intercept(Provider $provider): Provider;

    public function isInjectable(): bool;
}
