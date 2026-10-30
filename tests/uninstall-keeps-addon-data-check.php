<?php

/**
 * Deleting Tiers must not wipe settings an add-on still stores in
 * `tiers_settings` while that add-on is on disk. Whichever plugin is deleted
 * last removes the option.
 *
 * Core's bulk delete runs both uninstalls in one request and caches the plugin
 * list, so the check also covers a cache that still lists an add-on whose files
 * are already gone.
 *
 * Run: php tests/uninstall-keeps-addon-data-check.php
 */

declare(strict_types=1);

define('ABSPATH', sys_get_temp_dir() . '/');
define('WP_UNINSTALL_PLUGIN', 'plogins-tiers/tiers.php');

$deleted = [];
$disk    = [];
$cache   = null;

function is_multisite(): bool
{
    return false;
}
function wp_cache_delete(string $key, string $group): bool
{
    if ('plugins' === $key && 'plugins' === $group) {
        $GLOBALS['cache'] = null;
    }

    return true;
}
function get_plugins(): array
{
    return $GLOBALS['cache'] ?? $GLOBALS['disk'];
}
function delete_option(string $name): bool
{
    $GLOBALS['deleted'][] = $name;

    return true;
}
function delete_metadata(string $type, int $id, string $key, string $value = '', bool $all = false): bool
{
    $GLOBALS['deleted'][] = $type . ':' . $key;

    return true;
}

$free  = ['plogins-tiers/tiers.php' => ['TextDomain' => 'plogins-tiers']];
$addon = ['any-folder/plogins-tiers-pro.php' => ['TextDomain' => 'plogins-tiers-pro']];

/** @return list<string> */
function run_uninstall(array $disk, ?array $cache): array
{
    $GLOBALS['deleted'] = [];
    $GLOBALS['disk']    = $disk;
    $GLOBALS['cache']   = $cache;
    include dirname(__DIR__) . '/uninstall.php';
    $out = $GLOBALS['deleted'];
    sort($out);

    return $out;
}

$all  = ['tiers_settings', 'user:tiers_pro_banner_dismissed'];
$kept = ['user:tiers_pro_banner_dismissed'];

$cases = [
    'add-on on disk keeps tiers_settings'           => [run_uninstall($free + $addon, null), $kept],
    'no add-on deletes everything'                   => [run_uninstall($free, null), $all],
    'stale cache listing a deleted add-on is ignored' => [run_uninstall($free, $free + $addon), $all],
];

$failed = false;
foreach ($cases as $name => [$got, $want]) {
    if ($got !== $want) {
        $failed = true;
        fwrite(STDERR, "FAIL: {$name}: deleted " . implode(', ', $got) . "\n");
    }
}
if ($failed) {
    exit(1);
}

echo "OK: uninstall keeps tiers_settings only while an add-on is on disk\n";
