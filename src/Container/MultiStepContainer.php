<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container;

use Vivarium\Comparator\Priority;

interface MultiStepContainer extends Container
{
    public function withStep(Step $step, int $prioirty = Priority::NORMAL): self;
}
