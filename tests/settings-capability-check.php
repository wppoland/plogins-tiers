<?php

/**
 * The settings screen is shown to manage_woocommerce and saves through
 * options.php, which checks manage_options unless the option group filters
 * option_page_capability_<group>. Without it a shop manager sees the screen
 * and every save dies with "Sorry, you are not allowed to manage options".
 *
 * Run: php tests/settings-capability-check.php
 */

declare(strict_types=1);

define('ABSPATH', sys_get_temp_dir() . '/');

$filters = [];
$groups  = [];

function add_action(string $hook, mixed $cb, int $prio = 10, int $args = 1): void {}
function add_filter(string $hook, callable $cb, int $prio = 10, int $args = 1): void { $GLOBALS['filters'][$hook] = $cb; }
function register_setting(string $group, string $option, array $args = []): void { $GLOBALS['groups'][] = $group; }
function add_settings_section(mixed ...$a): void {}
function add_settings_field(mixed ...$a): void {}
function __(string $s, string $d = ''): string { return $s; }
function esc_html__(string $s, string $d = ''): string { return $s; }

require __DIR__ . '/../autoload.php';

$settings = (new ReflectionClass(Tiers\Admin\Settings::class))->newInstanceWithoutConstructor();
$settings->registerHooks();
$settings->register_settings();

if ([] === $groups) {
    fwrite(STDERR, "FAIL: no register_setting() group found\n");
    exit(1);
}
foreach ($groups as $group) {
    $cb = $filters['option_page_capability_' . $group] ?? null;
    if (null === $cb || 'manage_woocommerce' !== $cb('manage_options')) {
        fwrite(STDERR, "FAIL: option group '{$group}' saves only for manage_options\n");
        exit(1);
    }
}

echo 'OK: ' . implode(', ', $groups) . " saves for manage_woocommerce\n";
