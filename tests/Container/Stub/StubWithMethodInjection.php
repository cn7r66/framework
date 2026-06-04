<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

declare(strict_types=1);

namespace Vivarium\Test\Container\Stub;

final class StubWithMethodInjection
{
    private StubService|null $service = null;

    public function setService(StubService $service): void
    {
        $this->service = $service;
    }

    public function withService(StubService $service): static
    {
        $clone          = clone $this;
        $clone->service = $service;

        return $clone;
    }

    public function getService(): StubService|null
    {
        return $this->service;
    }
}
