<?php
/**
 * Boot order: services listed here are resolved from the container and have
 * their registerHooks() called during Plugin::boot(). Each must implement
 * Tiers\Contract\HasHooks.
 *
 * @package Tiers
 *
 * @return array<class-string>
 */

declare(strict_types=1);

use Tiers\Service\TiersService;

defined('ABSPATH') || exit;

return [
    TiersService::class,
];
