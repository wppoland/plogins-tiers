/**
 * Tiers, live pricing table.
 *
 * Highlights the tier row that matches the quantity box and prints the line
 * total under the table. No dependencies: jQuery is only listened to when
 * WooCommerce has it on the page, because theme +/- buttons and the variation
 * form announce changes through jQuery events, not native ones.
 *
 * The table is complete without this script; it only adds the highlight and
 * the total.
 */
( function () {
	'use strict';

	function roundTo( amount, decimals ) {
		var factor = Math.pow( 10, decimals );
		return Math.round( amount * factor ) / factor;
	}

	/**
	 * Format an amount the way wc_price() does, as plain text.
	 */
	function formatPrice( amount, cfg ) {
		var decimals = parseInt( cfg.decimals, 10 ) || 0;
		var parts = roundTo( amount, decimals ).toFixed( decimals ).split( '.' );
		var whole = parts[ 0 ].replace( /\B(?=(\d{3})+(?!\d))/g, cfg.thousandSep || '' );
		var number = parts.length > 1 ? whole + ( cfg.decimalSep || '.' ) + parts[ 1 ] : whole;

		return String( cfg.priceFormat || '%1$s%2$s' )
			.replace( '%1$s', cfg.symbol || '' )
			.replace( '%2$s', number );
	}

	/**
	 * Index of the deepest tier the quantity reaches, or -1 below the first.
	 *
	 * @param {number[]} minQtys Tier minimums, ascending.
	 * @param {number}   qty     Quantity in the box.
	 */
	function tierIndex( minQtys, qty ) {
		var match = -1;

		minQtys.forEach( function ( min, i ) {
			if ( qty >= min ) {
				match = i;
			}
		} );

		return match;
	}

	/**
	 * Tier unit price, mirroring the cart: the discount comes off the regular
	 * price and never lifts the price above the current one.
	 */
	function tierUnitPrice( regular, current, percent, decimals ) {
		var base = regular > 0 ? regular : current;
		var price = roundTo( base * ( 1 - percent / 100 ), decimals );

		return current > 0 && price > current ? current : price;
	}

	if ( typeof module !== 'undefined' && module.exports ) {
		module.exports = {
			formatPrice: formatPrice,
			tierIndex: tierIndex,
			tierUnitPrice: tierUnitPrice,
		};
		return;
	}

	var cfg = window.tiersTable || {};

	function init( table ) {
		var productId = table.getAttribute( 'data-product-id' );
		var marker = productId
			? document.querySelector( 'form.cart [name="add-to-cart"][value="' + productId + '"]' )
			: null;
		var form = marker ? marker.closest( 'form' ) : null;
		var input = form ? form.querySelector( 'input.qty, input[name="quantity"]' ) : null;
		var line = table.querySelector( '.tiers-line-price' );
		var rows = Array.prototype.slice.call( table.querySelectorAll( 'tr[data-min-qty]' ) );

		if ( ! input || ! line || ! rows.length ) {
			return;
		}

		var decimals = parseInt( cfg.decimals, 10 ) || 0;
		var minQtys = rows.map( function ( row ) {
			return parseInt( row.getAttribute( 'data-min-qty' ), 10 ) || 0;
		} );
		var initialUnits = rows.map( function ( row ) {
			return row.hasAttribute( 'data-unit-price' ) ? parseFloat( row.getAttribute( 'data-unit-price' ) ) : null;
		} );
		var initialBase = table.hasAttribute( 'data-base-price' ) ? parseFloat( table.getAttribute( 'data-base-price' ) ) : null;
		var initialCells = rows.map( function ( row ) {
			return Array.prototype.map.call( row.querySelectorAll( '.tiers-price, .tiers-saved' ), function ( cell ) {
				return cell.innerHTML;
			} );
		} );
		var units = initialUnits.slice();
		var base = initialBase;

		function update() {
			var qty = Math.max( 0, parseInt( input.value, 10 ) || 0 );
			var index = tierIndex( minQtys, qty );
			var unit = index >= 0 ? units[ index ] : base;

			rows.forEach( function ( row, i ) {
				row.classList.toggle( 'is-active-tier', i === index );
			} );

			if ( qty < 1 || null === unit || isNaN( unit ) ) {
				line.textContent = '';
				return;
			}

			line.textContent = String( cfg.totalFormat || '%1$s: %2$s' )
				.replace( '%1$s', String( qty ) )
				.replace( '%2$s', formatPrice( unit * qty, cfg ) );
		}

		// A picked variation has one price, so the table can show exact
		// prices for it instead of the range across all variations.
		function showVariation( variation ) {
			var current = parseFloat( variation.display_price );
			var regular = parseFloat( variation.display_regular_price ) || current;

			if ( isNaN( current ) ) {
				return;
			}

			base = current;
			units = rows.map( function ( row, i ) {
				var percent = parseFloat( row.getAttribute( 'data-percent' ) ) || 0;
				var unit = tierUnitPrice( regular, current, percent, decimals );
				var price = row.querySelector( '.tiers-price' );
				var saved = row.querySelector( '.tiers-saved' );

				if ( price ) {
					price.textContent = formatPrice( unit, cfg );
				}
				if ( saved ) {
					saved.textContent = formatPrice( roundTo( regular - unit, decimals ), cfg );
				}

				return unit;
			} );
			update();
		}

		function resetVariation() {
			base = initialBase;
			units = initialUnits.slice();
			rows.forEach( function ( row, i ) {
				row.querySelectorAll( '.tiers-price, .tiers-saved' ).forEach( function ( cell, c ) {
					cell.innerHTML = initialCells[ i ][ c ];
				} );
			} );
			update();
		}

		input.addEventListener( 'input', update );
		input.addEventListener( 'change', update );

		if ( window.jQuery ) {
			window.jQuery( input ).on( 'change', update );
			window.jQuery( form ).on( 'found_variation', function ( event, variation ) {
				showVariation( variation );
			} );
			window.jQuery( form ).on( 'reset_data', resetVariation );
		}

		update();
	}

	document.querySelectorAll( '.tiers-pricing-table' ).forEach( init );
} )();
