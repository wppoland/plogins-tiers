# Tiers

Tiers adds volume pricing to WooCommerce products. Define quantity thresholds and the discount to apply when a customer reaches them, and a clean, server-rendered pricing table shows the available discounts right on the product page.

## Features

- Define unlimited global pricing tiers (minimum quantity → discount percentage).
- Server-rendered volume pricing table on single product pages — no jQuery, no layout shift.
- Highest matching tier wins: 12 units gets the "10+" discount, not the "5+" one.
- Choose where the table appears, or place it manually with the `[tiers_table]` shortcode or "Volume pricing table" block.
- Optional "You save" column and per-line cart note.
- Works with WooCommerce coupons and taxes; compatible with HPOS and Cart/Checkout Blocks.

## Installation

1. Install and activate WooCommerce (8.0 or later).
2. Upload the `tiers` folder to `/wp-content/plugins/`, or install from the WordPress plugin directory.
3. Activate the plugin through the **Plugins** screen.
4. Go to **WooCommerce → Tiers** and add at least one pricing tier (e.g. 5 units → 5% off).

## Frequently Asked Questions

**Does the pricing table reload the page?**
No. It is server-rendered and loads with the page, before any JavaScript runs.

**Are discounts compatible with WooCommerce coupons?**
Yes. Tiers modifies the line-item price before WooCommerce calculates totals, so coupons work normally on top of the tiered price.

---

Built by WPPoland — https://plogins.com

License: GPL-2.0-or-later
