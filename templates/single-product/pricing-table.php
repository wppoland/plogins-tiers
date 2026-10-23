<?php
/**
 * Pricing tiers table template.
 *
 * @var \WC_Product                                                          $tiers_product
 * @var list<array{min_qty: int, discount_percent: float, label: string}>    $tiers_tiers
 * @var string                                                               $tiers_heading       Optional table heading.
 * @var bool                                                                 $tiers_show_savings  Whether to render a savings column.
 * @var array<string, mixed>                                                 $tiers_prices        Display prices from TiersService::display_prices().
 *
 * @package Tiers/Templates
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$tiers_heading      = isset( $tiers_heading ) ? (string) $tiers_heading : '';
$tiers_show_savings = ! empty( $tiers_show_savings );

if ( empty( $tiers_tiers ) ) :
	?>
	<div class="tiers-pricing-table">
		<?php if ( '' !== $tiers_heading ) : ?>
			<p class="tiers-pricing-table__heading"><?php echo esc_html( $tiers_heading ); ?></p>
		<?php endif; ?>
		<p class="tiers-pricing-table__empty">
			<?php esc_html_e( 'No volume discounts are available for this product yet.', 'plogins-tiers' ); ?>
		</p>
	</div>
	<?php
	return;
endif;

// Identify the tier with the deepest discount so we can flag it as the best deal.
$tiers_best_i       = 0;
$tiers_best_percent = 0.0;
foreach ( $tiers_tiers as $tiers_idx => $tiers_row ) {
	$tiers_row_pct = (float) $tiers_row['discount_percent'];
	if ( $tiers_row_pct > $tiers_best_percent ) {
		$tiers_best_percent = $tiers_row_pct;
		$tiers_best_i       = $tiers_idx;
	}
}

// Display prices (incl. or excl. tax, as the shop shows them), worked out per
// variation for a variable product. Where variations differ, a cell shows the
// range until the shopper picks one.
$tiers_rows      = isset( $tiers_prices['rows'] ) && is_array( $tiers_prices['rows'] ) ? $tiers_prices['rows'] : array();
$tiers_base_min  = (float) ( $tiers_prices['base_min'] ?? 0 );
$tiers_base_max  = (float) ( $tiers_prices['base_max'] ?? 0 );
$tiers_fmt_range = static fn( float $min, float $max ): string => $min === $max ? wc_price( $min ) : wc_format_price_range( $min, $max );
?>
<div class="tiers-pricing-table" data-product-id="<?php echo esc_attr( (string) $tiers_product->get_id() ); ?>" aria-label="<?php esc_attr_e( 'Volume pricing', 'plogins-tiers' ); ?>"<?php echo $tiers_base_min === $tiers_base_max ? ' data-base-price="' . esc_attr( (string) $tiers_base_min ) . '"' : ''; ?>>
	<?php if ( '' !== $tiers_heading ) : ?>
		<p class="tiers-pricing-table__heading"><?php echo esc_html( $tiers_heading ); ?></p>
	<?php endif; ?>
	<table>
		<caption class="screen-reader-text">
			<?php echo '' !== $tiers_heading ? esc_html( $tiers_heading ) : esc_html__( 'Volume pricing tiers', 'plogins-tiers' ); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Quantity', 'plogins-tiers' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Discount', 'plogins-tiers' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Price', 'plogins-tiers' ); ?></th>
				<?php if ( $tiers_show_savings ) : ?>
					<th scope="col"><?php esc_html_e( 'You save', 'plogins-tiers' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ( $tiers_tiers as $tiers_i => $tiers_tier ) :
				$tiers_percent  = (float) $tiers_tier['discount_percent'];
				$tiers_row      = $tiers_rows[ $tiers_i ] ?? array();
				$tiers_price_lo = (float) ( $tiers_row['price_min'] ?? 0 );
				$tiers_price_hi = (float) ( $tiers_row['price_max'] ?? 0 );
				$tiers_is_last  = ( count( $tiers_tiers ) - 1 === $tiers_i );
				$tiers_next_min = $tiers_is_last ? null : $tiers_tiers[ $tiers_i + 1 ]['min_qty'] - 1;
				$tiers_is_best  = ( $tiers_i === $tiers_best_i && count( $tiers_tiers ) > 1 );
				?>
				<tr<?php echo $tiers_is_best ? ' class="is-best-tier"' : ''; ?> data-min-qty="<?php echo esc_attr( (string) $tiers_tier['min_qty'] ); ?>" data-percent="<?php echo esc_attr( (string) $tiers_percent ); ?>"<?php echo $tiers_price_lo === $tiers_price_hi ? ' data-unit-price="' . esc_attr( (string) $tiers_price_lo ) . '"' : ''; ?>>
					<td>
						<?php
						if ( null !== $tiers_next_min ) {
							printf(
								/* translators: 1: min quantity, 2: max quantity */
								esc_html_x( '%1$d-%2$d', 'quantity range', 'plogins-tiers' ),
								esc_html( (string) $tiers_tier['min_qty'] ),
								esc_html( (string) $tiers_next_min ),
							);
						} else {
							printf(
								/* translators: %d: minimum quantity */
								esc_html_x( '%d+', 'quantity, no upper limit', 'plogins-tiers' ),
								esc_html( (string) $tiers_tier['min_qty'] ),
							);
						}

						if ( '' !== $tiers_tier['label'] ) :
							?>
							<span class="tiers-tier-label"><?php echo esc_html( $tiers_tier['label'] ); ?></span>
							<?php
						endif;

						if ( $tiers_is_best ) :
							?>
							<span class="tiers-best-badge"><?php esc_html_e( 'Best value', 'plogins-tiers' ); ?></span>
							<?php
						endif;
						?>
					</td>
					<td>
						<span class="tiers-discount">
						<?php
						printf(
							/* translators: %s: discount percentage */
							esc_html__( '%s%% off', 'plogins-tiers' ),
							esc_html( (string) $tiers_percent )
						);
						?>
						</span>
					</td>
					<td class="tiers-price"><?php echo wp_kses_post( $tiers_fmt_range( $tiers_price_lo, $tiers_price_hi ) ); ?></td>
					<?php if ( $tiers_show_savings ) : ?>
						<td class="tiers-saved"><?php echo wp_kses_post( $tiers_fmt_range( (float) ( $tiers_row['saved_min'] ?? 0 ), (float) ( $tiers_row['saved_max'] ?? 0 ) ) ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php // Filled by assets/js/tiers-table.js as the quantity changes. Its height is reserved in CSS, so filling it shifts nothing. ?>
	<p class="tiers-line-price" aria-live="polite"></p>
</div>
