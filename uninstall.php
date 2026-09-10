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

// The PRO banner's dismissal is stored per user, so it belongs to the
// plugin rather than to the site content. User meta is global, not
// per-site, which is why this uses delete_metadata's \$delete_all rather
// than a loop over the users of one blog.
delete_metadata('user', 0, 'tiers_pro_banner_dismissed', '', true);
