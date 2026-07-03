<?php

declare(strict_types=1);

/**
 * PHPStan bootstrap: define plugin constants so tiers.php and src/ can be analysed
 * without a running WordPress environment.
 */

define( 'ABSPATH', '/tmp/' );
define( 'Tiers\VERSION', '0.1.0' );
define( 'Tiers\PLUGIN_FILE', '/tmp/tiers.php' );
define( 'Tiers\PLUGIN_DIR', '/tmp' );
define( 'Tiers\MIN_PHP_VERSION', '8.1.0' );
define( 'Tiers\MIN_WC_VERSION', '8.0.0' );
define( 'TIERS_DIR', '/tmp/' );
define( 'TIERS_URL', 'https://example.test/wp-content/plugins/tiers/' );
