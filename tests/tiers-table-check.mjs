// Pure helpers of assets/js/tiers-table.js: row matching, tier unit price
// and price formatting. Run: node tests/tiers-table-check.mjs
import assert from 'node:assert/strict';
import { createRequire } from 'node:module';

const require = createRequire( import.meta.url );
const { tierIndex, tierUnitPrice, formatPrice } = require( '../assets/js/tiers-table.js' );

assert.equal( tierIndex( [ 5, 10 ], 1 ), -1, 'below the first tier: no row' );
assert.equal( tierIndex( [ 5, 10 ], 5 ), 0, 'qty 5 reaches 5+' );
assert.equal( tierIndex( [ 5, 10 ], 9 ), 0, 'qty 9 stays on 5+' );
assert.equal( tierIndex( [ 5, 10 ], 12 ), 1, 'qty 12 reaches 10+' );

assert.equal( tierUnitPrice( 10, 10, 10, 2 ), 9, '10% off 10.00' );
assert.equal( tierUnitPrice( 10, 7, 20, 2 ), 7, 'a sale price below the tier price wins' );
assert.equal( tierUnitPrice( 0, 10, 10, 2 ), 9, 'no regular price: discount the current one' );

const cfg = { decimals: 2, decimalSep: ',', thousandSep: '.', priceFormat: '%2$s %1$s', symbol: 'zl' };
assert.equal( formatPrice( 1234.5, cfg ), '1.234,50 zl' );
assert.equal( formatPrice( 9, { decimals: 0, priceFormat: '%1$s%2$s', symbol: '$' } ), '$9' );

console.log( 'OK: tiers-table helpers' );
