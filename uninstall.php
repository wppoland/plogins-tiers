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

/**
 * Remove this plugin's options from the current site.
 */
function tiers_uninstall_site(): void {
	delete_option( 'tiers_settings' );
}

// Options are per site, so a network uninstall has to visit every site.
if ( is_multisite() ) {
	foreach ( get_sites( array( 'fields' => 'ids', 'number' => 0 ) ) as $tiers_site_id ) {
		switch_to_blog( (int) $tiers_site_id );
		tiers_uninstall_site();
		restore_current_blog();
	}
	unset( $tiers_site_id );
} else {
	tiers_uninstall_site();
}

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'tiers_pro_banner_dismissed', '', true);
