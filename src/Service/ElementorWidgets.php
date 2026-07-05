<?php
/**
 * Elementor integration service.
 *
 * Registers the Tiers Elementor widget(s). The `elementor/widgets/register`
 * action only fires when Elementor is active, so this service is self-guarding:
 * nothing loads unless Elementor is present.
 *
 * @package Tiers\Service
 */

declare(strict_types=1);

namespace Tiers\Service;

defined( 'ABSPATH' ) || exit;

use Tiers\Contract\HasHooks;
use Tiers\Elementor\TiersTableWidget;

/**
 * Wires the Tiers widgets into the Elementor editor.
 */
final class ElementorWidgets implements HasHooks {

	/**
	 * Register WordPress hooks.
	 */
	public function registerHooks(): void {
		add_action( 'elementor/widgets/register', array( $this, 'register' ) );
	}

	/**
	 * Register widget instances with Elementor's widgets manager.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
	 */
	public function register( $widgets_manager ): void {
		// Loaded here (not autoloaded) so \Elementor\Widget_Base always exists.
		require_once __DIR__ . '/../Elementor/TiersTableWidget.php';
		$widgets_manager->register( new TiersTableWidget() );
	}
}
