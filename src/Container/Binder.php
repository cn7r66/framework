<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

/** @template T */
interface Binder
{
    /** @return T */
    public function to(string $type, string $tag = Binding::DEFAULT, string $context = Binding::GLOBAL);

    /** @return T */
    public function toInstance(mixed $instance);

    /**
     * @return T
     */
    public function toProvider(Provider $provider);
}
