<?php
/**
 * Constants needed by PHPStan to analyse the plugin without bootstrapping WordPress.
 *
 * @package Tiers
 */

declare(strict_types=1);

namespace {
    if (! defined('ABSPATH')) {
        define('ABSPATH', '/tmp/wordpress/');
    }
    if (! defined('TIERS_DIR')) {
        define('TIERS_DIR', '/tmp/tiers/');
    }
    if (! defined('TIERS_URL')) {
        define('TIERS_URL', 'https://example.test/wp-content/plugins/tiers/');
    }
}

namespace Tiers {
    if (! defined('Tiers\\VERSION')) {
        define('Tiers\\VERSION', '0.1.0');
    }
    if (! defined('Tiers\\PLUGIN_FILE')) {
        define('Tiers\\PLUGIN_FILE', '/tmp/tiers/tiers.php');
    }
}
