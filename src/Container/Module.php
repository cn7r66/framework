<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

/** @template T of Solver */
interface Module
{
    /** 
     * @param T $solver
     * 
     * @return T
     */
    public function install($solver);
}
