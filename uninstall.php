<?php
/**
 * Tiers uninstall routine.
 *
 * Removes plugin options when the user deletes the plugin.
 * Tiers does not create custom tables; settings are in wp_options only.
 *
 * @package Tiers
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'tiers_settings' );
