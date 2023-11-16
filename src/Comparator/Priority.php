<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Comparator;

interface Priority
{
    public const VERY_VERY_LOW = -1000;

    public const VERY_LOW = -100;

    public const LOW = -50;

    public const NORMAL = 0;

    public const HIGH = 50;

    public const VERY_HIGH = 100;

    public const VERY_VERY_HIGH = 1000;
}
