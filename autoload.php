<?php
/**
 * PSR-4 autoloader for the Tiers plugin.
 *
 * Maps the Tiers\ namespace to src/, with no Composer overhead.
 *
 * @package Tiers
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

spl_autoload_register(
	static function ( string $class_name ): void {
		if ( 0 !== strncmp( $class_name, 'Tiers\\', 6 ) ) {
			return;
		}

		$relative = str_replace( '\\', '/', substr( $class_name, 6 ) );
		$base_dir = __DIR__ . '/src/';
		$file     = $base_dir . $relative . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);
