<?php
/**
 * PRO upsell content, generated from the plogins.com registry by
 * scripts/gen-pro-upsell.mjs. The admin upsell renders this; curate the
 * feature list to fit this plugin's settings screen (do not invent features).
 *
 * @package plogins-tiers-pro
 */

defined( 'ABSPATH' ) || exit;

return [
	'name'       => 'Tiers Pro',
	'url'        => 'https://plogins.com/plogins-tiers-pro/pricing/',
	'sellable'   => true,
	'price_from' => 29,
	'currency'   => 'EUR',
	'price_pln'  => 129,
	'lead'       => [
		'en' => 'Role pricing, per-product overrides, category rules, B2B wholesale and scheduled pricing ship in the current PRO release.',
		'pl' => 'Ceny per rola, nadpisania per produkt, reguły kategorii, hurt B2B i harmonogramy są dostępne w bieżącym wydaniu PRO.',
	],
	'features'   => [
		[
			'en' => [
				'title' => 'Role and group pricing',
				'desc'  => 'Limit discount tiers to selected WordPress roles (e.g. wholesale, customer) or guests, in global Tiers settings.',
			],
			'pl' => [
				'title' => 'Ceny per rola i grupa',
				'desc'  => 'Ogranicz progi rabatowe do wybranych ról WordPress (np. wholesale, customer) lub gości, w ustawieniach globalnych Tiers.',
			],
		],
		[
			'en' => [
				'title' => 'Per-product overrides',
				'desc'  => 'Product-specific tier tables instead of global rules, a Tiers tab on the product editor.',
			],
			'pl' => [
				'title' => 'Nadpisania per produkt',
				'desc'  => 'Własna tabela progów na karcie produktu zamiast reguł globalnych, osobna zakładka Tiers w edytorze produktu.',
			],
		],
		[
			'en' => [
				'title' => 'Category pricing rules',
				'desc'  => 'Volume breaks for selected WooCommerce categories, percent or fixed amount off, optional label and allowed roles.',
			],
			'pl' => [
				'title' => 'Reguły kategorii',
				'desc'  => 'Progi ilościowe dla wybranych kategorii WooCommerce, rabat procentowy lub kwotowy, etykieta i dozwolone role.',
			],
		],
		[
			'en' => [
				'title' => 'B2B wholesale',
				'desc'  => 'Wholesale roles, VAT exemption, minimum order quantity, hidden prices and a request-a-quote form.',
			],
			'pl' => [
				'title' => 'Hurt B2B',
				'desc'  => 'Role hurtowe, zwolnienie z VAT, minimalna ilość zamówienia, ukryte ceny i formularz zapytania ofertowego.',
			],
		],
		[
			'en' => [
				'title' => 'Scheduled pricing',
				'desc'  => 'Time-boxed tier campaigns with start/end windows, scope targeting and optional allowed roles.',
			],
			'pl' => [
				'title' => 'Harmonogramy cen',
				'desc'  => 'Kampanie czasowe z oknem start/koniec, zakresem (wszystkie produkty, kategorie lub ID) i opcjonalnymi rolami.',
			],
		],
	],
];
