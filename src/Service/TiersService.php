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
	 * Supported placement hooks for the product-page pricing table.
	 *
	 * Maps a stored placement key to a WooCommerce single-product action hook
	 * and its priority. `shortcode` is intentionally absent: it disables the
	 * automatic placement so the table only renders via the block/shortcode.
	 *
	 * @return array<string, array{hook: string, priority: int}>
	 */
	public static function placements(): array {
		return array(
			'summary'      => array(
				'hook'     => 'woocommerce_single_product_summary',
				'priority' => 26,
			),
			'before_cart'  => array(
				'hook'     => 'woocommerce_before_add_to_cart_form',
				'priority' => 10,
			),
			'after_cart'   => array(
				'hook'     => 'woocommerce_after_add_to_cart_form',
				'priority' => 10,
			),
			'product_meta' => array(
				'hook'     => 'woocommerce_product_meta_start',
				'priority' => 5,
			),
		);
	}

	/**
	 * Register WordPress hooks.
	 */
	public function registerHooks(): void {
		add_action( 'woocommerce_before_calculate_totals', array( $this, 'apply_cart_discounts' ), 25 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

		$settings  = $this->global_settings();
		$placement = (string) ( $settings['placement'] ?? 'summary' );

		if ( ( $settings['show_table'] ?? true ) && 'shortcode' !== $placement ) {
			$placements = self::placements();
			$target     = $placements[ $placement ] ?? $placements['summary'];
			add_action( $target['hook'], array( $this, 'render_pricing_table' ), $target['priority'] );
		}

		// "From X" hint under shop-loop prices. Added at the start of each loop
		// tile and removed at its end, so it never reaches the single product
		// price, the cart or the mini-cart.
		if ( $settings['loop_hint'] ?? true ) {
			add_action( 'woocommerce_before_shop_loop_item', array( $this, 'start_loop_hint' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'stop_loop_hint' ), 99 );

			// Block themes render the grid with the Product Collection block,
			// which fires neither loop hook: start at its price block instead.
			add_filter( 'render_block_context', array( $this, 'start_block_loop_hint' ), 10, 2 );
			add_filter( 'render_block_woocommerce/product-price', array( $this, 'stop_block_loop_hint' ) );

			// The same hint in Tujo's quick-order table, when Tujo is active.
			add_filter( 'rapid/product_price_html', array( $this, 'render_loop_hint' ), 10, 2 );
		}

		// Per-line savings note in the cart (display only).
		if ( $settings['cart_savings_note'] ?? false ) {
			add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'render_cart_savings_note' ), 10, 3 );
		}

		// Shortcode + block render callback (always available).
		add_shortcode( 'tiers_table', array( $this, 'render_shortcode' ) );

		// Boot runs on `init`; register the block on the same hook. If we are
		// already inside init, hook a slightly later priority so it still fires.
		if ( did_action( 'init' ) ) {
			add_action( 'init', array( $this, 'register_block' ), 20 );
		} else {
			add_action( 'init', array( $this, 'register_block' ) );
		}
	}

	/**
	 * Register the server-rendered Tiers table block.
	 */
	public function register_block(): void {
		$metadata = \Tiers\PLUGIN_DIR . '/blocks/tiers-table';

		if ( function_exists( 'register_block_type' ) && file_exists( $metadata . '/block.json' ) ) {
			register_block_type(
				$metadata,
				array( 'render_callback' => array( $this, 'render_block' ) )
			);
		}
	}

	/**
	 * Register and conditionally enqueue front-end assets (no jQuery).
	 *
	 * The style and script are registered unconditionally so the shortcode and
	 * block can enqueue them on demand, and auto-enqueued on single product
	 * pages when a table will be shown.
	 */
	public function enqueue_assets(): void {
		$this->register_style();

		if ( ! is_product() ) {
			return;
		}

		$tiers = $this->get_active_tiers();

		if ( empty( $tiers ) || ! ( $this->global_settings()['show_table'] ?? true ) ) {
			return;
		}

		$this->enqueue_table_assets();
	}

	/**
	 * Enqueue the table style and the quantity-tracking script.
	 */
	private function enqueue_table_assets(): void {
		$this->register_style();
		wp_enqueue_style( 'tiers-pricing' );
		wp_enqueue_script( 'tiers-table' );
	}

	/**
	 * Register the front-end style if it has not been registered yet.
	 *
	 * Registration normally happens on `wp_enqueue_scripts`, but shortcode and
	 * block render paths can run before (or without) that hook, e.g. a block
	 * server-render during a REST editor preview. Registering on demand keeps
	 * enqueue_style() from silently no-opping in those contexts.
	 */
	private function register_style(): void {
		if ( wp_style_is( 'tiers-pricing', 'registered' ) ) {
			return;
		}

		wp_register_style(
			'tiers-pricing',
			\Tiers\Plugin::instance()->url( 'assets/css/tiers.css' ),
			array(),
			\Tiers\VERSION,
		);

		wp_register_script(
			'tiers-table',
			\Tiers\Plugin::instance()->url( 'assets/js/tiers-table.js' ),
			array(),
			\Tiers\VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			),
		);

		wp_add_inline_script(
			'tiers-table',
			'window.tiersTable = ' . wp_json_encode(
				array(
					'decimals'     => wc_get_price_decimals(),
					'decimalSep'   => wc_get_price_decimal_separator(),
					'thousandSep'  => wc_get_price_thousand_separator(),
					'priceFormat'  => html_entity_decode( get_woocommerce_price_format(), ENT_QUOTES, 'UTF-8' ),
					'symbol'       => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
					/* translators: 1: quantity, 2: formatted line total */
					'totalFormat'  => __( 'Total for %1$s: %2$s', 'plogins-tiers' ),
				)
			) . ';',
			'before',
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

		$contents   = $cart->get_cart();
		$quantities = self::group_quantities( $contents );

		foreach ( $contents as $item ) {
			$product = $item['data'] ?? null;

			if ( ! $product instanceof \WC_Product ) {
				continue;
			}

			$qty = self::tier_quantity( $item, $quantities );

			if ( $qty <= 0 ) {
				continue;
			}

			$tiers = $this->get_active_tiers_for_product( $product );

			if ( empty( $tiers ) ) {
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

			// Never raise the price: if the product is already cheaper (e.g. on
			// sale, or a deeper tier already ran in an earlier pass of this
			// multi-fire hook), leave the lower price in place. This also keeps
			// the operation idempotent and avoids double-discounting.
			$current = (float) $product->get_price();

			if ( $current > 0 && $discounted >= $current ) {
				continue;
			}

			$product->set_price( (string) $discounted );
		}
	}

	/**
	 * Render the pricing table on single product pages (auto placement).
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

		echo $this->get_table_html( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Output is escaped inside the template.
	}

	/**
	 * Render the `[tiers_table]` shortcode.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 * @return string Rendered table HTML, or empty string when nothing to show.
	 */
	public function render_shortcode( array|string $atts = array() ): string {
		$atts = shortcode_atts(
			array( 'product_id' => 0 ),
			is_array( $atts ) ? $atts : array(),
			'tiers_table',
		);

		$product = $this->resolve_product( (int) $atts['product_id'] );

		if ( ! $product instanceof \WC_Product ) {
			return '';
		}

		$this->enqueue_table_assets();

		return $this->get_table_html( $product );
	}

	/**
	 * Render the Tiers table block (server-side).
	 *
	 * @param array<string, mixed> $attributes Block attributes.
	 * @return string Rendered table HTML.
	 */
	public function render_block( array $attributes = array() ): string {
		$product_id = isset( $attributes['productId'] ) ? (int) $attributes['productId'] : 0;
		$product    = $this->resolve_product( $product_id );

		if ( ! $product instanceof \WC_Product ) {
			return '';
		}

		$this->enqueue_table_assets();

		// The wrapper carries the block's align and custom class names.
		return sprintf( '<div %s>%s</div>', get_block_wrapper_attributes(), $this->get_table_html( $product ) );
	}

	/**
	 * Append a per-line "You save" note to the cart item subtotal.
	 *
	 * Display only, no pricing logic. The discount itself is applied in
	 * apply_cart_discounts().
	 *
	 * @param string               $subtotal  Formatted subtotal HTML.
	 * @param array<string, mixed> $cart_item Cart item data.
	 * @param string               $cart_item_key Cart item key (unused).
	 * @return string
	 */
	public function render_cart_savings_note( string $subtotal, array $cart_item, string $cart_item_key ): string {
		unset( $cart_item_key );

		$product = $cart_item['data'] ?? null;
		$qty     = (int) ( $cart_item['quantity'] ?? 0 );

		if ( ! $product instanceof \WC_Product || $qty <= 0 ) {
			return $subtotal;
		}

		// The tier is chosen by the quantity across all variations of the
		// product, the same count apply_cart_discounts() used.
		$tier_qty = WC()->cart instanceof \WC_Cart
			? self::tier_quantity( $cart_item, self::group_quantities( WC()->cart->get_cart() ) )
			: $qty;
		$tiers    = $this->get_active_tiers_for_product( $product );
		$match    = empty( $tiers ) ? null : $this->find_matching_tier( $tiers, $tier_qty, $product );

		if ( null === $match ) {
			return $subtotal;
		}

		$regular = (float) $product->get_regular_price();

		if ( $regular <= 0 ) {
			return $subtotal;
		}

		// Measure the saving the way the cart line is displayed (incl. or excl.
		// tax), or a gross cart line carries a net "You save" amount.
		$incl       = WC()->cart instanceof \WC_Cart && WC()->cart->display_prices_including_tax();
		$to_display = static fn( float $price ): float => (float) ( $incl
			? wc_get_price_including_tax( $product, array( 'qty' => $qty, 'price' => $price ) )
			: wc_get_price_excluding_tax( $product, array( 'qty' => $qty, 'price' => $price ) ) );
		$saved      = round( $to_display( $regular ) - $to_display( (float) $product->get_price() ), wc_get_price_decimals() );

		if ( $saved <= 0 ) {
			return $subtotal;
		}

		return $subtotal . '<br /><small class="tiers-cart-savings">' . sprintf(
			/* translators: %s: formatted saved amount */
			esc_html__( 'You save %s', 'plogins-tiers' ),
			wp_kses_post( wc_price( $saved ) )
		) . '</small>';
	}

	/**
	 * Build the pricing-table HTML for a product (shared by all render paths).
	 *
	 * @param \WC_Product $product The product to render the table for.
	 * @return string Captured template output, or empty string when no tiers.
	 */
	private function get_table_html( \WC_Product $product ): string {
		$tiers = $this->get_active_tiers_for_product( $product );

		if ( empty( $tiers ) ) {
			return '';
		}

		$settings = $this->global_settings();

		ob_start();
		$this->template_loader->include(
			'single-product/pricing-table',
			array(
				'product'      => $product,
				'tiers'        => $tiers,
				'heading'      => (string) ( $settings['table_heading'] ?? '' ),
				'show_savings' => (bool) ( $settings['show_savings'] ?? false ),
				'prices'       => $this->display_prices( $product, $tiers ),
			),
		);

		return (string) ob_get_clean();
	}

	/**
	 * Start adding the tier hint to prices inside a shop-loop tile.
	 */
	public function start_loop_hint(): void {
		add_filter( 'woocommerce_get_price_html', array( $this, 'render_loop_hint' ), 20, 2 );
	}

	/**
	 * Stop adding the tier hint once the shop-loop tile is done.
	 */
	public function stop_loop_hint(): void {
		remove_filter( 'woocommerce_get_price_html', array( $this, 'render_loop_hint' ), 20 );
	}

	/**
	 * Start the hint for a price block inside a Product Collection grid.
	 *
	 * @param array<string, mixed> $context      Block context.
	 * @param array<string, mixed> $parsed_block The block about to render.
	 * @return array<string, mixed>
	 */
	public function start_block_loop_hint( array $context, array $parsed_block ): array {
		if ( 'woocommerce/product-price' === ( $parsed_block['blockName'] ?? '' ) && isset( $context['query']['isProductCollectionBlock'] ) ) {
			$this->start_loop_hint();
		}

		return $context;
	}

	/**
	 * Stop the hint once a price block has rendered.
	 *
	 * @param string $block_content Rendered block.
	 * @return string
	 */
	public function stop_block_loop_hint( string $block_content ): string {
		$this->stop_loop_hint();

		return $block_content;
	}

	/**
	 * Append "From X" under a loop price when a tier makes it cheaper.
	 *
	 * X is the lowest price the deepest tier reaches, shown the way the shop
	 * displays prices (incl. or excl. tax).
	 *
	 * @param string      $price_html Formatted price HTML.
	 * @param \WC_Product $product    The product in the loop tile.
	 * @return string
	 */
	public function render_loop_hint( string $price_html, \WC_Product $product ): string {
		// No price shown (hidden by a catalog or quote plugin, or a notice in its
		// place), or already hinted (a quick-order table inside a loop tile).
		if ( ! str_contains( $price_html, 'woocommerce-Price-amount' ) || str_contains( $price_html, 'tiers-loop-hint' ) ) {
			return $price_html;
		}

		$tiers = $this->get_active_tiers_for_product( $product );

		if ( empty( $tiers ) ) {
			return $price_html;
		}

		$prices = $this->display_prices( $product, $tiers );

		if ( null === $prices['lowest'] ) {
			return $price_html;
		}

		return $price_html . '<br /><small class="tiers-loop-hint">' . sprintf(
			/* translators: %s: lowest price per unit with the deepest volume discount */
			esc_html__( 'From %s in bulk', 'plogins-tiers' ),
			wp_kses_post( wc_price( $prices['lowest'] ) )
		) . '</small>';
	}

	/**
	 * Tier prices for a product, as the shop displays them.
	 *
	 * A variable product is priced per variation: its parent carries no regular
	 * price, and its get_price() is only the cheapest variation, so basing the
	 * table on the parent gave every variation the cheapest one's prices.
	 *
	 * @param \WC_Product                                                        $product The product.
	 * @param list<array{min_qty: int, discount_percent: float, label: string}> $tiers   Tiers sorted ASC by min_qty.
	 * @return array{rows: list<array{price_min: float, price_max: float, saved_min: float, saved_max: float}>, base_min: float, base_max: float, lowest: float|null}
	 */
	public function display_prices( \WC_Product $product, array $tiers ): array {
		if ( $product instanceof \WC_Product_Variable ) {
			// Display prices (tax applied per variation), so no conversion below.
			$prices   = $product->get_variation_prices( true );
			$variants = array();

			foreach ( $prices['price'] as $id => $current ) {
				$variants[] = array( (float) ( $prices['regular_price'][ $id ] ?? 0 ), (float) $current );
			}

			$to_display = static fn( float $price ): float => $price;
		} else {
			$variants   = array( array( (float) $product->get_regular_price(), (float) $product->get_price() ) );
			$to_display = static fn( float $price ): float => (float) wc_get_price_to_display( $product, array( 'price' => $price ) );
		}

		return self::tier_prices( $variants, $tiers, wc_get_price_decimals(), $to_display );
	}

	/**
	 * Tier prices across product variants (pure, no WordPress calls).
	 *
	 * Mirrors apply_cart_discounts(): the tier discount is taken off the regular
	 * price (the current price when there is none) and never raises the price
	 * above the current one.
	 *
	 * @param list<array{0: float, 1: float}>                                   $variants   [regular, current] raw prices per variant.
	 * @param list<array{min_qty: int, discount_percent: float, label: string}> $tiers      Tiers sorted ASC by min_qty.
	 * @param int                                                               $decimals   Price decimals.
	 * @param callable(float): float                                            $to_display Converts a raw price to a display price.
	 * @return array{rows: list<array{price_min: float, price_max: float, saved_min: float, saved_max: float}>, base_min: float, base_max: float, lowest: float|null}
	 */
	public static function tier_prices( array $variants, array $tiers, int $decimals, callable $to_display ): array {
		$rows     = array();
		$base_all = array();

		foreach ( $variants as $variant ) {
			$base_all[] = $to_display( $variant[1] );
		}

		foreach ( $tiers as $tier ) {
			$percent = (float) $tier['discount_percent'];
			$prices  = array();
			$saved   = array();

			foreach ( $variants as $variant ) {
				$current = $variant[1];
				$base    = $variant[0] > 0 ? $variant[0] : $current;
				$raw     = round( $base * ( 1.0 - $percent / 100.0 ), $decimals );

				if ( $current > 0 && $raw > $current ) {
					$raw = $current;
				}

				$price    = $to_display( $raw );
				$prices[] = $price;
				$saved[]  = round( $to_display( $base ) - $price, $decimals );
			}

			$rows[] = array(
				'price_min' => $prices ? min( $prices ) : 0.0,
				'price_max' => $prices ? max( $prices ) : 0.0,
				'saved_min' => $saved ? min( $saved ) : 0.0,
				'saved_max' => $saved ? max( $saved ) : 0.0,
			);
		}

		$base_min = $base_all ? min( $base_all ) : 0.0;
		$lowest   = $rows ? min( array_column( $rows, 'price_min' ) ) : null;

		return array(
			'rows'     => $rows,
			'base_min' => $base_min,
			'base_max' => $base_all ? max( $base_all ) : 0.0,
			// Only a hint worth showing: a tier that actually goes below the price.
			'lowest'   => null !== $lowest && $lowest > 0 && $lowest < $base_min ? $lowest : null,
		);
	}

	/**
	 * Quantity per product across the cart, variations counted under their parent.
	 *
	 * @param array<array-key, mixed> $contents Cart contents.
	 * @return array<int, int> Product (or parent) id => total quantity.
	 */
	public static function group_quantities( array $contents ): array {
		$totals = array();

		foreach ( $contents as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$id            = (int) ( $item['product_id'] ?? 0 );
			$totals[ $id ] = ( $totals[ $id ] ?? 0 ) + (int) ( $item['quantity'] ?? 0 );
		}

		return $totals;
	}

	/**
	 * The quantity that picks a cart line's tier.
	 *
	 * `product_id` on a cart line is the parent for a variation, so 3 red and
	 * 3 blue of one shirt reach a 5+ tier together.
	 *
	 * @param array<array-key, mixed> $item       Cart line.
	 * @param array<int, int>         $quantities Output of group_quantities().
	 * @return int
	 */
	public static function tier_quantity( array $item, array $quantities ): int {
		$id = (int) ( $item['product_id'] ?? 0 );

		return $id > 0 && isset( $quantities[ $id ] ) ? $quantities[ $id ] : (int) ( $item['quantity'] ?? 0 );
	}

	/**
	 * Resolve a product from an explicit id, falling back to the global product.
	 *
	 * @param int $product_id Explicit product id, or 0 to use the current product.
	 * @return \WC_Product|null
	 */
	private function resolve_product( int $product_id ): ?\WC_Product {
		if ( $product_id > 0 ) {
			$product = wc_get_product( $product_id );
			return $product instanceof \WC_Product ? $product : null;
		}

		global $product;

		return $product instanceof \WC_Product ? $product : null;
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

			$sanitized_tier = array(
				'min_qty'          => $min_qty,
				'discount_percent' => $percent,
				'label'            => sanitize_text_field( (string) ( $tier['label'] ?? '' ) ),
			);

			/**
			 * Filter a single active pricing tier row.
			 *
			 * PRO uses this to load extra parameters (e.g. allowed_roles).
			 *
			 * @param array{min_qty: int, discount_percent: float, label: string} $sanitized_tier Sanitized tier.
			 * @param array<string, mixed>                                         $tier           Raw tier data.
			 */
			$tiers[] = apply_filters( 'tiers_active_tier', $sanitized_tier, $tier );
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
