<?php
/**
 * Volume pricing tiers service.
 *
 * @package Tiers\Service
 */

declare(strict_types=1);

namespace Tiers\Service;

defined( 'ABSPATH' ) || exit;

use Tiers\Contract\HasHooks;
use Tiers\Util\TemplateLoader;

/**
 * Volume / quantity-based pricing tiers for WooCommerce.
 *
 * Reads tiers from the `tiers_settings` option, applies line-item discounts
 * in the cart, and renders a pricing table on the product page.
 *
 * The highest matching tier wins (not cumulative).
 *
 * @package Tiers\Service
 */
final class TiersService implements HasHooks {

	private const OPTION = 'tiers_settings';

	/**
	 * Constructor.
	 *
	 * @param TemplateLoader $template_loader Template loader utility.
	 */
	public function __construct(
		private readonly TemplateLoader $template_loader,
	) {}

	/**
	 * Register WordPress hooks.
	 */
	public function registerHooks(): void {
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_cart_discounts' ), 25 );
		add_action( 'woocommerce_single_product_summary', array( $this, 'render_pricing_table' ), 26 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue front-end assets on single product pages (CSS only).
	 */
	public function enqueue_assets(): void {
		if ( ! is_product() ) {
			return;
		}

		$tiers = $this->get_active_tiers();

		if ( empty( $tiers ) || ! ( $this->global_settings()['show_table'] ?? true ) ) {
			return;
		}

		wp_enqueue_style(
			'tiers-pricing',
			\Tiers\Plugin::instance()->url( 'assets/css/tiers.css' ),
			array(),
			\Tiers\VERSION,
		);
	}

	/**
	 * Apply the highest matching tier discount to each cart line item.
	 *
	 * @param \WC_Cart $cart The WooCommerce cart instance.
	 */
	public function apply_cart_discounts( \WC_Cart $cart ): void {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return;
		}

		$tiers = $this->get_active_tiers();

		if ( empty( $tiers ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $item ) {
			$product = $item['data'] ?? null;

			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			$qty = (int) ( $item['quantity'] ?? 0 );

			if ( $qty <= 0 ) {
				continue;
			}

			$matching_tier = $this->find_matching_tier( $tiers, $qty, $product );

			if ( null === $matching_tier ) {
				continue;
			}

			$percent = (float) ( $matching_tier['discount_percent'] ?? 0 );

			if ( $percent <= 0 ) {
				continue;
			}

			$regular = (float) $product->get_regular_price();

			if ( $regular <= 0 ) {
				$regular = (float) $product->get_price();
			}

			if ( $regular <= 0 ) {
				continue;
			}

			$discounted = round( $regular * ( 1.0 - $percent / 100.0 ), wc_get_price_decimals() );
			$product->set_price( (string) $discounted );
		}
	}

	/**
	 * Render the pricing table below the price on single product pages.
	 */
	public function render_pricing_table(): void {
		global $product;

		if ( ! $product instanceof \WC_Product ) {
			return;
		}

		$settings = $this->global_settings();

		if ( ! ( $settings['show_table'] ?? true ) ) {
			return;
		}

		$tiers = $this->get_active_tiers_for_product( $product );

		if ( empty( $tiers ) ) {
			return;
		}

		$this->template_loader->include(
			'single-product/pricing-table',
			array(
				'product' => $product,
				'tiers'   => $tiers,
			),
		);
	}

	/**
	 * Returns all configured pricing tiers, sorted by min_qty ASC.
	 *
	 * @return list<array{min_qty: int, discount_percent: float, label: string}>
	 */
	public function get_active_tiers(): array {
		$settings = $this->global_settings();
		$raw      = $settings['tiers'] ?? array();

		if ( ! is_array( $raw ) || empty( $raw ) ) {
			return array();
		}

		$tiers = array();

		foreach ( $raw as $tier ) {
			if ( ! is_array( $tier ) ) {
				continue;
			}

			$min_qty = (int) ( $tier['min_qty'] ?? 0 );
			$percent = (float) ( $tier['discount_percent'] ?? 0 );

			if ( $min_qty <= 0 || $percent <= 0 || $percent > 100 ) {
				continue;
			}

			$tiers[] = array(
				'min_qty'          => $min_qty,
				'discount_percent' => $percent,
				'label'            => sanitize_text_field( (string) ( $tier['label'] ?? '' ) ),
			);
		}

		usort( $tiers, static fn( array $a, array $b ): int => $a['min_qty'] <=> $b['min_qty'] );

		return $tiers;
	}

	/**
	 * Returns tiers for a specific product (allows PRO overrides via filter).
	 *
	 * @param \WC_Product $product The product being viewed or carted.
	 * @return list<array{min_qty: int, discount_percent: float, label: string}>
	 */
	public function get_active_tiers_for_product( \WC_Product $product ): array {
		/**
		 * Filter the pricing tiers for a specific product.
		 *
		 * PRO uses this to inject per-product tier overrides.
		 *
		 * @param list<array{min_qty: int, discount_percent: float, label: string}> $tiers   Global tiers.
		 * @param \WC_Product                                                        $product The product.
		 */
		return (array) apply_filters( 'tiers_product_tiers', $this->get_active_tiers(), $product );
	}

	/**
	 * Finds the highest-matching tier for the given quantity.
	 *
	 * @param list<array{min_qty: int, discount_percent: float, label: string}> $tiers   Tiers sorted ASC by min_qty.
	 * @param int                                                               $qty     Cart item quantity.
	 * @param \WC_Product                                                       $product The product (used for per-product overrides).
	 * @return array{min_qty: int, discount_percent: float, label: string}|null
	 */
	private function find_matching_tier( array $tiers, int $qty, \WC_Product $product ): ?array {
		$tiers = $this->get_active_tiers_for_product( $product );
		$match = null;

		foreach ( $tiers as $tier ) {
			if ( $qty >= $tier['min_qty'] ) {
				$match = $tier;
			}
		}

		return $match;
	}

	/**
	 * Returns the raw settings array from the database.
	 *
	 * @return array<string, mixed>
	 */
	private function global_settings(): array {
		$settings = get_option( self::OPTION, array() );

		return is_array( $settings ) ? $settings : array();
	}
}
