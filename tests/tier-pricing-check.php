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

	if ( $failures ) {
		fwrite( STDERR, "FAIL\n - " . implode( "\n - ", $failures ) . "\n" );
		exit( 1 );
	}

	echo "OK: tier quantity and tier prices\n";
}
