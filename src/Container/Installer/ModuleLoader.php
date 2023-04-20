<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 *
 */

namespace Vivarium\Container\Installer;

use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Solver;
use Vivarium\Container\Solver\MultiStepSolver;

final class ModuleLoader
{
    /** @var Sequence<Module> */
    private Sequence $modules;

    public function __construct(Module ...$modules)
    {
        $this->modules = ArraySequence::fromArray($modules);
    }

    public function install(Module $module): ModuleLoader
    {
        $loader         = clone $this;
        $loader->modules = $loader->modules
            ->add($module);

        return $loader;
    }

    public function getSolver(): Solver
    {
        $installer = new Installer();
        foreach ($this->modules as $module) {
            $installer = $module->install($installer);
        }

        return new MultiStepSolver(...$installer->getSteps());
    }
}
