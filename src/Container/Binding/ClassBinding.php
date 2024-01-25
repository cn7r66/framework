<?php

declare(strict_types=1);

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MIT
 * Copyright (c) 2023 Luca Cantoreggi
 */

namespace Vivarium\Container\Binding;

use Vivarium\Assertion\String\IsClassOrInterface;
use Vivarium\Collection\Sequence\ArraySequence;
use Vivarium\Collection\Sequence\Sequence;
use Vivarium\Container\Binding;

use function array_merge;
use function class_implements;
use function get_parent_class;

final class ClassBinding extends BaseBinding
{
    /** @var class-string */
    private string $class;

    public static function fromBinding(Binding $binding): ClassBinding
    {
        if ($binding instanceof ClassBinding) {
            return $binding;
        }

        return new ClassBinding(
            $binding->getId(),
            $binding->getTag(),
            $binding->getContext()
        );
    }

    /** @psalm-assert class-string $id */
    public function __construct(string $id, string $tag = self::DEFAULT, string $context = self::GLOBAL)
    {
        (new IsClassOrInterface())
            ->assert($id);

        $this->class = $id;

        parent::__construct($id, $tag, $context);
    }

    /** @return class-string */
    public function getId(): string
    {
        return $this->class;
    }

    /** @return Sequence<Binding> */
    public function hierarchy(): Sequence
    {
        $bindings = array_merge(
            [$this],
            $this->extends(),
            $this->interfaces(),
        );

        /** @var Sequence<Binding> $hierarchy */
        $hierarchy = new ArraySequence();
        foreach ($bindings as $binding) {
            $hierarchy = $hierarchy->add($binding);
            while ($binding->couldBeWidened()) {
                $binding   = $binding->widen();
                $hierarchy = $hierarchy->add($binding);
            }
        }

        return $hierarchy;
    }

    /** @return array<Binding> */
    private function extends(): array
    {
        $extends = [];

        $extend = get_parent_class($this->class);
        while ($extend !== false) {
            $extends[] = new ClassBinding(
                $extend,
                Binding::DEFAULT,
                $this->getContext()
            );

            $extend = get_parent_class($extend);
        }

        return $extends;
    }

    /** @return array<Binding> */
    private function interfaces(): array
    {
        $interfaces = [];
        foreach (class_implements($this->class) as $interface) {
            $interfaces[] = new ClassBinding(
                $interface,
                Binding::DEFAULT,
                $this->getContext()
            );
        }

        return $interfaces;
    }
}
