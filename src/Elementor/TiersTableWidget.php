<?php
/**
 * Elementor widget: Volume Pricing Table.
 *
 * A thin wrapper around the [tiers_table] shortcode so the pricing table can be
 * placed with the Elementor editor. Kept deliberately minimal (renders the
 * shortcode) so a future migration to Elementor v4 atomic widgets is localized
 * to this class. Loaded only from the `elementor/widgets/register` hook, so the
 * `\Elementor\Widget_Base` base class is guaranteed to exist here.
 *
 * @package Tiers\Elementor
 */

declare(strict_types=1);

namespace Tiers\Elementor;

defined( 'ABSPATH' ) || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

/**
 * Volume Pricing Table Elementor widget.
 */
final class TiersTableWidget extends Widget_Base {

	/**
	 * Widget machine name.
	 */
	public function get_name(): string {
		return 'tiers_table';
	}

	/**
	 * Widget label shown in the editor.
	 */
	public function get_title(): string {
		return esc_html__( 'Volume Pricing Table', 'tiers' );
	}

	/**
	 * Editor panel icon.
	 */
	public function get_icon(): string {
		return 'eicon-table';
	}

	/**
	 * Editor panel categories.
	 *
	 * @return string[]
	 */
	public function get_categories(): array {
		return array( 'woocommerce-elements', 'general' );
	}

	/**
	 * Search keywords in the editor.
	 *
	 * @return string[]
	 */
	public function get_keywords(): array {
		return array( 'tiers', 'pricing', 'volume', 'quantity', 'discount', 'woocommerce' );
	}

	/**
	 * Register the editor controls.
	 */
	protected function register_controls(): void {
		$this->start_controls_section(
			'content',
			array( 'label' => esc_html__( 'Pricing table', 'tiers' ) )
		);

		$this->add_control(
			'product_id',
			array(
				'label'       => esc_html__( 'Product ID', 'tiers' ),
				'type'        => Controls_Manager::NUMBER,
				'default'     => 0,
				'min'         => 0,
				'description' => esc_html__( 'Leave 0 to use the current product on a product page.', 'tiers' ),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget on the front end and in the editor preview.
	 */
	protected function render(): void {
		$settings   = $this->get_settings_for_display();
		$product_id = isset( $settings['product_id'] ) ? absint( $settings['product_id'] ) : 0;

		echo do_shortcode( sprintf( '[tiers_table product_id="%d"]', $product_id ) );
	}
}
