<?php
/**
 * Tiers service container registration.
 *
 * Returns a callable that binds every service into the container.
 * Bindings are lazy; admin services are guarded by is_admin().
 *
 * @package Tiers
 */

declare(strict_types=1);

namespace Tiers;

defined( 'ABSPATH' ) || exit;

use Tiers\Admin\Settings;
use Tiers\Service\ElementorWidgets;
use Tiers\Service\TiersService;
use Tiers\Util\TemplateLoader;

return static function ( Container $c ): void {
	// Utilities.
	$c->singleton( TemplateLoader::class, static fn(): TemplateLoader => new TemplateLoader() );

	// Core service.
	$c->singleton(
		TiersService::class,
		static fn(): TiersService => new TiersService(
			$c->get( TemplateLoader::class ),
		)
	);

	// Elementor integration (self-guards on the elementor/widgets/register hook).
	$c->singleton( ElementorWidgets::class, static fn(): ElementorWidgets => new ElementorWidgets() );

	// Admin (only in wp-admin context).
	if ( is_admin() ) {
		$c->singleton(
			Settings::class,
			static fn(): Settings => new Settings(
				$c->get( TiersService::class ),
			)
		);
	}
};
