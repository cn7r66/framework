<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

/** @template T */
interface Interceptor
{
    /**
     * @param callable(InstanceMethod): InstanceMethod $configure 
     * @return T 
     */
    public function withMethod(callable $configure);

    /**
     * @param callable(InstanceMethod):InstanceMethod $configure
     * @return T
     */
    public function withImmutableMethod(callable $configure);

    /**
     * @param callable(InstanceMethod):InstanceMethod $configure
     * @return T
     */
    public function withUniqueMethod(callable $configure);

    /**
     * @param callable(InstanceMethod):InstanceMethod $configure
     * @return T
     */
    public function withUniqueImmutableMethod(callable $configure);

    /** @return T */
    public function withInterception(Interception $interception);

    /** @return T */
    public function withUniqueInterception(Interception $interception);
}
