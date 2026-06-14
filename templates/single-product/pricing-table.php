<?php
/**
 * Pricing tiers table template.
 *
 * @var \WC_Product                                                          $tiers_product
 * @var list<array{min_qty: int, discount_percent: float, label: string}>    $tiers_tiers
 * @var string                                                               $tiers_heading       Optional table heading.
 * @var bool                                                                 $tiers_show_savings  Whether to render a savings column.
 *
 * @package Tiers/Templates
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

if ( empty( $tiers_tiers ) ) {
	return;
}

$tiers_heading      = isset( $tiers_heading ) ? (string) $tiers_heading : '';
$tiers_show_savings = ! empty( $tiers_show_savings );
?>
<div class="tiers-pricing-table" aria-label="<?php esc_attr_e( 'Volume pricing', 'tiers' ); ?>">
	<?php if ( '' !== $tiers_heading ) : ?>
		<p class="tiers-pricing-table__heading"><?php echo esc_html( $tiers_heading ); ?></p>
	<?php endif; ?>
	<table>
		<caption class="screen-reader-text">
			<?php echo '' !== $tiers_heading ? esc_html( $tiers_heading ) : esc_html__( 'Volume pricing tiers', 'tiers' ); ?>
		</caption>
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Quantity', 'tiers' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Discount', 'tiers' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Price', 'tiers' ); ?></th>
				<?php if ( $tiers_show_savings ) : ?>
					<th scope="col"><?php esc_html_e( 'You save', 'tiers' ); ?></th>
				<?php endif; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$tiers_base_price = (float) $tiers_product->get_regular_price();
			if ( $tiers_base_price <= 0 ) {
				$tiers_base_price = (float) $tiers_product->get_price();
			}

			foreach ( $tiers_tiers as $tiers_i => $tiers_tier ) :
				$tiers_percent    = (float) $tiers_tier['discount_percent'];
				$tiers_tier_price = round( $tiers_base_price * ( 1.0 - $tiers_percent / 100.0 ), wc_get_price_decimals() );
				$tiers_saved      = round( $tiers_base_price - $tiers_tier_price, wc_get_price_decimals() );
				$tiers_is_last    = ( count( $tiers_tiers ) - 1 === $tiers_i );
				$tiers_next_min   = $tiers_is_last ? null : $tiers_tiers[ $tiers_i + 1 ]['min_qty'] - 1;
				?>
				<tr>
					<td>
						<?php
						if ( null !== $tiers_next_min ) {
							printf(
								/* translators: 1: min quantity, 2: max quantity */
								esc_html_x( '%1$d – %2$d', 'quantity range', 'tiers' ),
								esc_html( (string) $tiers_tier['min_qty'] ),
								esc_html( (string) $tiers_next_min ),
							);
						} else {
							printf(
								/* translators: %d: minimum quantity */
								esc_html_x( '%d+', 'quantity, no upper limit', 'tiers' ),
								esc_html( (string) $tiers_tier['min_qty'] ),
							);
						}
						?>
					</td>
					<td>
						<?php
						printf(
							/* translators: %s: discount percentage */
							esc_html__( '%s%% off', 'tiers' ),
							esc_html( (string) $tiers_percent )
						);
						?>
					</td>
					<td><?php echo wp_kses_post( wc_price( $tiers_tier_price ) ); ?></td>
					<?php if ( $tiers_show_savings ) : ?>
						<td><?php echo wp_kses_post( wc_price( $tiers_saved ) ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
