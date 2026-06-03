<?php

/*
 * This file is part of Vivarium
 * SPDX-License-Identifier: MPL-2.0
 * Copyright (c) The Vivarium Project
 */

namespace Vivarium\Core;

use DateTime;
use Throwable;
use Vivarium\Assertion\Conditional\Not;
use Vivarium\Assertion\Hierarchy\IsAssignableTo;
use Vivarium\Assertion\String\IsEmpty;
use Vivarium\Container\Registry\EagerRegistry;
use Vivarium\Container\FileCache;
use Vivarium\Container\Injector;
use Vivarium\Container\JsonCollector;
use Vivarium\Core\Event\AppEnd;
use Vivarium\Core\Event\AppStart;
use Vivarium\Dispatcher\EventDispatcher;

final class Kernel
{
    public static function boot(string $appRoot, string $appConfig): void
    {
        try {
            (new Not(new IsEmpty()))
                ->assert($appRoot);

            (new Not(new IsEmpty()))
                ->assert($appConfig);

            self::start(
                Config::loadFromFile(join(DIRECTORY_SEPARATOR, [$appRoot, $appConfig]))
            );
        } 
        catch (Throwable $ex) {
            $error = sprintf(
                '[%s] %s',
                (new DateTime())->format('d/M/Y:H:i:s O'),
                $ex->getMessage()
            );

            if (! is_dir($appRoot)) {
                mkdir($appRoot, 0755, true);
            }

            $result = file_put_contents(
                join(DIRECTORY_SEPARATOR, [$appRoot, 'kernel.log']),
                [$error, PHP_EOL],
                FILE_APPEND | LOCK_EX
            );

            if ($result === false) {
                error_log($error);
            }

            if (PHP_SAPI !== 'cli' && ! headers_sent()) {
                header(($_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1') . ' 503 Service Unavailable', true, 503);
                
                exit('<h1>503 Service Unavailable</h1>');
            }

            exit(1);
        }
    }

    private static function start(Config $config) : void
    {
        $injector = new Injector(function() use ($config) {
            $registry = new EagerRegistry();

            $T = \get_class($registry);
            foreach ($config->getModules() as $module) {
                $registry = (new $module())->configure($registry);

                (new IsAssignableTo($T))
                    ->assert($registry);
            }

            return $registry;
        });

        if ($config->isMetadataEnabled()) {
            $injector = $injector->withCollector(
                new JsonCollector($config->getMetadataPath())
            );
        }

        if ($config->isCacheEnabled()) {
            $injector = $injector->withFileCache(
                new FileCache($config->getCachePath())
            );
        }

        $dispatcher = $injector->get(EventDispatcher::class);
        
        $exitCode = $dispatcher
            ->dispatch(new AppStart())
            ->getExitCode();

        $exitCode = $dispatcher
            ->dispatch(new AppEnd($exitCode))
            ->getExitCode();

        exit($exitCode);
    }
}
