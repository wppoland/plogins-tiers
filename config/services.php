<?php
/**
 * Service wiring. Returns a closure that registers every service in the
 * container. Keep services thin; product logic lives in storefront-kit engines
 * instantiated here with this plugin's text-domain / option prefix / asset URLs.
 *
 * @package PluginNamespace
 */

declare(strict_types=1);

use PluginNamespace\Container;
use PluginNamespace\Migrator;

defined('ABSPATH') || exit;

return static function (Container $c): void {
    $c->singleton(Migrator::class, static fn (): Migrator => new Migrator());

    // Example — wire a storefront-kit engine via a thin adapter:
    //
    // $c->singleton(WaitlistService::class, static function () use ($c): WaitlistService {
    //     return new WaitlistService(/* repository, settings, template loader */);
    // });
};
