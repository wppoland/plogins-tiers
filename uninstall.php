<?php
/**
 * Tiers uninstall routine.
 *
 * Removes plugin options when the user deletes the plugin.
 * Tiers does not create custom tables; settings are in wp_options only.
 * `tiers_settings` also holds an add-on's own per-tier settings, so it stays
 * while that add-on is on disk; whichever plugin is deleted last removes it.
 *
 * @package Tiers
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

// Core's bulk delete runs every uninstall in one request, and the cached plugin
// list would still show an add-on whose files were deleted a moment earlier.
wp_cache_delete( 'plugins', 'plugins' );

// Matched on the text domain, so a renamed add-on folder still counts. The
// plugin folders are shared by the whole network, so this is read once.
$tiers_addon_installed = in_array( 'plogins-tiers-pro', array_column( get_plugins(), 'TextDomain' ), true );

// Options are per site, so a network uninstall has to visit every site.
if ( ! $tiers_addon_installed ) {
	if ( is_multisite() ) {
		foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $tiers_site_id ) {
			switch_to_blog( (int) $tiers_site_id );
			delete_option( 'tiers_settings' );
			restore_current_blog();
		}
		unset( $tiers_site_id );
	} else {
		delete_option( 'tiers_settings' );
	}
}

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'tiers_pro_banner_dismissed', '', true);
