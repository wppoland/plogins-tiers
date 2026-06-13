<?php

declare(strict_types=1);

namespace Tiers\Service;

use Tiers\Contract\HasHooks;
use WPPoland\StorefrontKit\Pricing\DynamicPricingEngine;
use WPPoland\StorefrontKit\Pricing\PriceTier;

defined('ABSPATH') || exit;

/**
 * Thin adapter over the storefront-kit volume/tiered pricing engine.
 *
 * Injects this plugin's text-domain ('tiers') and option prefix ('tiers_')
 * into the namespace-neutral {@see DynamicPricingEngine}. All product logic
 * lives in the kit; this class only supplies localisation, option storage,
 * and template rendering.
 */
final class TiersService implements HasHooks
{
    private const OPTION = 'tiers_settings';

    private ?DynamicPricingEngine $engine = null;

    public function __construct()
    {
        // The engine ships with storefront-kit >= 1.1.0. When it is present,
        // wire it with this plugin's text-domain / option prefix. Otherwise
        // leave the service inert (see registerHooks()).
        if (! class_exists(DynamicPricingEngine::class)) {
            return;
        }

        $this->engine = new DynamicPricingEngine(
            'price-table',
            [
                'heading'  => __('Volume pricing', 'tiers'),
                'quantity' => __('Quantity', 'tiers'),
                'discount' => __('Discount', 'tiers'),
                'price'    => __('Price', 'tiers'),
            ],
            fn (): bool => $this->isEnabled(),
            fn (): array => $this->tiers(),
            function (string $template, array $context): void {
                $this->renderTemplate($template, $context);
            },
        );
    }

    public function registerHooks(): void
    {
        if ($this->engine instanceof DynamicPricingEngine) {
            $this->engine->registerHooks();
            return;
        }

        // TODO: storefront-kit < 1.1.0 has no DynamicPricingEngine. Bump the
        // `wppoland/storefront-kit` constraint (composer update) to enable
        // tiered pricing. No hooks are registered until the engine is present.
    }

    private function isEnabled(): bool
    {
        return (bool) ($this->settings()['enabled'] ?? false);
    }

    /**
     * Build the configured tiers from option storage.
     *
     * @return list<PriceTier>
     */
    private function tiers(): array
    {
        $rows = $this->settings()['tiers'] ?? [];

        if (! is_array($rows)) {
            return [];
        }

        $tiers = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $tier = PriceTier::fromArray($row);

            if ($tier instanceof PriceTier) {
                $tiers[] = $tier;
            }
        }

        return $tiers;
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        $stored = get_option(self::OPTION, []);

        if (! is_array($stored)) {
            $stored = [];
        }

        /** @var array<string, mixed> $defaults */
        $defaults = require TIERS_DIR . 'config/defaults.php';

        return array_merge($defaults, $stored);
    }

    /**
     * @param array<string, mixed> $context
     */
    private function renderTemplate(string $template, array $context): void
    {
        $file = TIERS_DIR . 'templates/' . $template . '.php';

        if (! is_readable($file)) {
            return;
        }

        extract($context, EXTR_SKIP);
        require $file;
    }
}
