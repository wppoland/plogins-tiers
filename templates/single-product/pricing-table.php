<?php
/**
 * Pricing tiers table template.
 *
 * @var \WC_Product                                                          $tiers_product
 * @var list<array{min_qty: int, discount_percent: float, label: string}>    $tiers_tiers
 *
 * @package Tiers/Templates
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

if (empty($tiers_tiers)) {
    return;
}
?>
<div class="tiers-pricing-table" aria-label="<?php esc_attr_e('Volume pricing', 'tiers'); ?>">
    <table>
        <caption class="screen-reader-text">
            <?php esc_html_e('Volume pricing tiers', 'tiers'); ?>
        </caption>
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e('Quantity', 'tiers'); ?></th>
                <th scope="col"><?php esc_html_e('Discount', 'tiers'); ?></th>
                <th scope="col"><?php esc_html_e('Price', 'tiers'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $basePrice = (float) $tiers_product->get_regular_price();
            if ($basePrice <= 0) {
                $basePrice = (float) $tiers_product->get_price();
            }

            foreach ($tiers_tiers as $i => $tier) :
                $percent    = (float) $tier['discount_percent'];
                $tierPrice  = round($basePrice * (1.0 - $percent / 100.0), wc_get_price_decimals());
                $label      = $tier['label'] !== ''
                    ? $tier['label']
                    : sprintf(
                        /* translators: %d: minimum quantity */
                        _n('%d+ unit', '%d+ units', $tier['min_qty'], 'tiers'),
                        $tier['min_qty'],
                    );
                $isLast    = ($i === count($tiers_tiers) - 1);
                $nextMinQty = $isLast ? null : $tiers_tiers[$i + 1]['min_qty'] - 1;
                ?>
                <tr>
                    <td>
                        <?php
                        if ($nextMinQty !== null) {
                            printf(
                                /* translators: 1: min quantity, 2: max quantity */
                                esc_html_x('%1$d – %2$d', 'quantity range', 'tiers'),
                                esc_html((string) $tier['min_qty']),
                                esc_html((string) $nextMinQty),
                            );
                        } else {
                            printf(
                                /* translators: %d: minimum quantity */
                                esc_html_x('%d+', 'quantity, no upper limit', 'tiers'),
                                esc_html((string) $tier['min_qty']),
                            );
                        }
                        ?>
                    </td>
                    <td><?php printf(esc_html__('%s%% off', 'tiers'), esc_html((string) $percent)); ?></td>
                    <td><?php echo wp_kses_post(wc_price($tierPrice)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
