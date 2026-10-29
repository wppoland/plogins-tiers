<?php
/**
 * Tier quantity and tier price math.
 *
 * 1. Variations of one product count together toward a tier (3 red + 3 blue
 *    reach a 5+ tier), in the cart discount itself, not only in a helper.
 * 2. A variable product is priced per variation. The table used to base every
 *    row on the parent, whose regular price is empty and whose get_price() is
 *    the cheapest variation, so a 100.00 variation was shown 10.00 prices.
 *
 * 3. The "From X in bulk" hint, also used for Tujo's quick-order table, is
 *    added once, and never where the price is hidden or replaced by a notice.
 *
 * Run: php tests/tier-pricing-check.php
 */

declare(strict_types=1);

// phpcs:disable
namespace {
	define( 'ABSPATH', __DIR__ . '/' );

	$GLOBALS['tiers_option'] = array(
		'tiers' => array(
			array( 'min_qty' => 5, 'discount_percent' => 10, 'label' => '' ),
			array( 'min_qty' => 10, 'discount_percent' => 20, 'label' => '' ),
		),
	);

	function get_option( $name, $default = false ) { return $GLOBALS['tiers_option']; }
	function apply_filters( $hook, $value, ...$args ) { return $value; }
	function sanitize_text_field( $text ) { return (string) $text; }
	function is_admin() { return false; }
	function wp_doing_ajax() { return false; }
	function wc_get_price_decimals() { return 2; }
	function wc_get_price_to_display( $product, $args ) { return (float) $args['price']; }
	function wc_price( $price ) { return '<span class="woocommerce-Price-amount amount">' . number_format( (float) $price, 2 ) . '</span>'; }
	function esc_html__( $text, $domain ) { return $text; }
	function wp_kses_post( $html ) { return $html; }

	class WC_Product {
		public function __construct( private float $regular, private float $price ) {}
		public function get_regular_price() { return (string) $this->regular; }
		public function get_price() { return (string) $this->price; }
		public function set_price( $price ) { $this->price = (float) $price; }
	}

	class WC_Cart {
		/** @param array<string, array<string, mixed>> $items */
		public function __construct( private array $items ) {}
		public function get_cart() { return $this->items; }
	}

	$root = dirname( __DIR__ );
	require $root . '/src/Contract/HasHooks.php';
	require $root . '/src/Service/TiersService.php';

	use Tiers\Service\TiersService;

	$failures = array();
	$check    = static function ( bool $ok, string $message ) use ( &$failures ): void {
		if ( ! $ok ) {
			$failures[] = $message;
		}
	};

	// 1. Variations of parent 7 add up; a lone simple product does not borrow them.
	$red    = new WC_Product( 10.0, 10.0 );
	$blue   = new WC_Product( 10.0, 10.0 );
	$simple = new WC_Product( 10.0, 10.0 );
	$cart   = new WC_Cart(
		array(
			'a' => array( 'product_id' => 7, 'variation_id' => 71, 'quantity' => 3, 'data' => $red ),
			'b' => array( 'product_id' => 7, 'variation_id' => 72, 'quantity' => 3, 'data' => $blue ),
			'c' => array( 'product_id' => 9, 'variation_id' => 0, 'quantity' => 3, 'data' => $simple ),
		)
	);

	$service = ( new ReflectionClass( TiersService::class ) )->newInstanceWithoutConstructor();
	$service->apply_cart_discounts( $cart );

	$check( 9.0 === (float) $red->get_price(), 'red variation: 3 + 3 of one product should reach the 5+ tier (9.00), got ' . $red->get_price() );
	$check( 9.0 === (float) $blue->get_price(), 'blue variation: 3 + 3 of one product should reach the 5+ tier (9.00), got ' . $blue->get_price() );
	$check( 10.0 === (float) $simple->get_price(), 'simple product with 3 units must stay at 10.00, got ' . $simple->get_price() );

	// 2. Per-variation table prices: a 10.00 and a 100.00 variation.
	$identity = static fn( float $p ): float => $p;
	$tiers    = $GLOBALS['tiers_option']['tiers'];
	$prices   = TiersService::tier_prices( array( array( 10.0, 10.0 ), array( 100.0, 100.0 ) ), $tiers, 2, $identity );

	$check( 9.0 === $prices['rows'][0]['price_min'] && 90.0 === $prices['rows'][0]['price_max'], '5+ row should range 9.00 to 90.00' );
	$check( 8.0 === $prices['rows'][1]['price_min'] && 80.0 === $prices['rows'][1]['price_max'], '10+ row should range 8.00 to 80.00' );
	$check( 8.0 === $prices['lowest'], 'deepest tier price ("From X") should be 8.00' );

	// A sale below the tier price is what the cart charges, and no hint then.
	$sale = TiersService::tier_prices( array( array( 10.0, 7.0 ) ), $tiers, 2, $identity );
	$check( 7.0 === $sale['rows'][1]['price_min'], 'a 7.00 sale price must cap the 8.00 tier price' );
	$check( null === $sale['lowest'], 'no "From X" hint when no tier goes below the current price' );

	// 3. The "From X" hint (shop loops and Tujo's quick-order table) is added once.
	$hinted = $service->render_loop_hint( wc_price( 10 ), $simple );
	$check( 1 === substr_count( $hinted, 'tiers-loop-hint' ) && str_contains( $hinted, '8.00' ), 'price HTML should get one "From 8.00 in bulk" hint, got ' . $hinted );
	$check( $hinted === $service->render_loop_hint( $hinted, $simple ), 'an already hinted price (quick-order table inside a loop tile) must not get a second hint' );
	$check( '' === $service->render_loop_hint( '', $simple ), 'a hidden (empty) price must stay empty' );
	$notice = '<span class="catalog-price-notice">Contact us for pricing</span>';
	$check( $notice === $service->render_loop_hint( $notice, $simple ), 'a notice shown instead of a hidden price must not reveal a bulk price' );

	if ( $failures ) {
		fwrite( STDERR, "FAIL\n - " . implode( "\n - ", $failures ) . "\n" );
		exit( 1 );
	}

	echo "OK: tier quantity and tier prices\n";
}
