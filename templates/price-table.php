<?php
/**
 * Volume pricing table, rendered on the single product page by the
 * storefront-kit DynamicPricingEngine.
 *
 * @package Tiers
 *
 * @var array<string, string> $labels Localised column labels.
 * @var list<array{min_quantity:int,discount_percent:float,price:float,price_html:string}> $rows
 */

declare(strict_types=1);

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Variables are local to the template include scope, not true globals.

defined('ABSPATH') || exit;

if (empty($rows) || ! is_array($rows)) {
    return;
}
?>
<table class="tiers-price-table">
    <caption><?php echo esc_html($labels['heading'] ?? ''); ?></caption>
    <thead>
        <tr>
            <th scope="col"><?php echo esc_html($labels['quantity'] ?? ''); ?></th>
            <th scope="col"><?php echo esc_html($labels['discount'] ?? ''); ?></th>
            <th scope="col"><?php echo esc_html($labels['price'] ?? ''); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row) : ?>
            <tr>
                <td><?php echo esc_html(sprintf('%d+', (int) $row['min_quantity'])); ?></td>
                <td><?php echo esc_html(sprintf('-%s%%', (string) (float) $row['discount_percent'])); ?></td>
                <td><?php echo wp_kses_post((string) $row['price_html']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
