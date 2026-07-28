<?php
/**
 * PRO upgrade promotion for the Tiers settings screen.
 *
 * @package Tiers\Admin
 */

declare(strict_types=1);

namespace Tiers\Admin;

defined( 'ABSPATH' ) || exit;

/**
 * PRO upgrade promotion, shown ONLY on the Tiers settings screen: a dismissible
 * top banner, a sidebar promo panel, and a "what PRO adds" locked-card list.
 *
 * It is pure advertising: no disabled form fields, nothing blocks a free
 * workflow, it is scoped to this one screen and the banner is dismissible per
 * user. That keeps it inside the WordPress.org guidelines (no admin hijacking,
 * no trialware). Content comes from config/pro-upsell.php, generated from the
 * plogins.com registry, so the feature copy always matches the real PRO edition.
 *
 * @package Tiers\Admin
 */
final class ProUpsell {

	private const META   = 'tiers_pro_banner_dismissed';
	private const ACTION = 'tiers_dismiss_pro';

	/**
	 * Cached upsell data loaded from config/pro-upsell.php.
	 *
	 * @var array<string, mixed>|null
	 */
	private ?array $data = null;

	/**
	 * Register the admin-post handler for the dismiss link.
	 */
	public function registerHooks(): void {
		add_action( 'admin_post_' . self::ACTION, array( $this, 'handleDismiss' ) );
	}

	/**
	 * Load and cache the packaged upsell data.
	 *
	 * @return array<string, mixed>
	 */
	private function data(): array {
		if ( null === $this->data ) {
			$file       = TIERS_DIR . 'config/pro-upsell.php';
			$this->data = is_readable( $file ) ? (array) require $file : array();
		}

		return $this->data;
	}

	/**
	 * Whether to render the promo at all (filterable for white-label builds).
	 */
	public function enabled(): bool {
		/**
		 * Filters whether the Tiers PRO promo is shown on the settings screen.
		 *
		 * @param bool $show Default true.
		 */
		return (bool) apply_filters( 'tiers/show_pro_cta', true ) && array() !== $this->features();
	}

	/**
	 * The URL the "Upgrade to PRO" buttons point at.
	 */
	private function url(): string {
		$default = (string) ( $this->data()['url'] ?? 'https://plogins.com/plogins-tiers-pro/pricing/' );

		/**
		 * Filters the URL the "Upgrade to PRO" buttons point at.
		 *
		 * @param string $url Default the Tiers PRO pricing page.
		 */
		return (string) apply_filters( 'tiers/pro_url', $default );
	}

	/**
	 * Whether the current locale is Polish.
	 */
	private function isPolish(): bool {
		return str_starts_with( (string) get_locale(), 'pl' );
	}

	/**
	 * A short "from …/yr" price label in the current locale, or an empty string.
	 */
	private function priceLabel(): string {
		$d = $this->data();

		if ( $this->isPolish() && ! empty( $d['price_pln'] ) ) {
			/* translators: %d: yearly price in PLN */
			return sprintf( __( 'od %d zł/rok', 'plogins-tiers' ), (int) $d['price_pln'] );
		}

		if ( ! empty( $d['price_from'] ) ) {
			$cur = ( $d['currency'] ?? 'EUR' ) === 'EUR' ? '€' : (string) $d['currency'] . ' ';
			/* translators: 1: currency symbol, 2: yearly price */
			return sprintf( __( 'from %1$s%2$d/yr', 'plogins-tiers' ), $cur, (int) $d['price_from'] );
		}

		return '';
	}

	/**
	 * The localised feature list from the packaged data.
	 *
	 * @return array<int, array{title: string, desc: string}>
	 */
	private function features(): array {
		$lang = $this->isPolish() ? 'pl' : 'en';
		$out  = array();

		foreach ( (array) ( $this->data()['features'] ?? array() ) as $f ) {
			$x = is_array( $f ) ? ( $f[ $lang ] ?? $f['en'] ?? null ) : null;

			if ( is_array( $x ) && ! empty( $x['title'] ) ) {
				$out[] = array(
					'title' => (string) $x['title'],
					'desc'  => (string) ( $x['desc'] ?? '' ),
				);
			}
		}

		return $out;
	}

	/**
	 * Whether the current user has dismissed the top banner.
	 */
	public function bannerDismissed(): bool {
		return (bool) get_user_meta( get_current_user_id(), self::META, true );
	}

	/**
	 * The nonce-protected admin-post URL that dismisses the banner.
	 */
	private function dismissUrl(): string {
		return wp_nonce_url( admin_url( 'admin-post.php?action=' . self::ACTION ), self::ACTION );
	}

	/**
	 * Handle the dismiss link: record it per user and redirect back.
	 */
	public function handleDismiss(): void {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'Permission denied.', 'plogins-tiers' ) );
		}

		check_admin_referer( self::ACTION );
		update_user_meta( get_current_user_id(), self::META, 1 );
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=tiers-settings' ) );
		exit;
	}

	// -- Render pieces --------------------------------------------------

	/**
	 * Dismissible strip at the top of the settings screen.
	 */
	public function banner(): void {
		if ( ! $this->enabled() || $this->bannerDismissed() ) {
			return;
		}

		$name     = (string) ( $this->data()['name'] ?? 'Tiers Pro' );
		$price    = $this->priceLabel();
		$subtitle = implode(
			', ',
			array_slice(
				array_map(
					static fn ( array $f ): string => $f['title'],
					$this->features(),
				),
				0,
				3,
			),
		);
		?>
		<div class="tiers-pro-banner" role="note">
			<span class="tiers-pro-banner__tag">PRO</span>
			<p class="tiers-pro-banner__text">
				<strong>
				<?php
				/* translators: %s: PRO edition name */
				printf( esc_html__( 'Do more with %s', 'plogins-tiers' ), esc_html( $name ) );
				?>
				</strong>
				<?php
				if ( '' !== $subtitle ) :
					?>
					<span class="tiers-pro-banner__sub"><?php echo esc_html( $subtitle ); ?></span><?php endif; ?>
				<?php
				if ( '' !== $price ) :
					?>
					<span class="tiers-pro-banner__price"><?php echo esc_html( $price ); ?></span><?php endif; ?>
			</p>
			<a class="button button-primary tiers-pro-banner__cta" href="<?php echo esc_url( $this->url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Upgrade to PRO', 'plogins-tiers' ); ?>
			</a>
			<a class="tiers-pro-banner__dismiss" href="<?php echo esc_url( $this->dismissUrl() ); ?>" aria-label="<?php esc_attr_e( 'Dismiss this notice', 'plogins-tiers' ); ?>">&times;</a>
		</div>
		<?php
	}

	/**
	 * Sidebar promo panel (sits in the settings two-column layout).
	 */
	public function aside(): void {
		if ( ! $this->enabled() ) {
			return;
		}

		$name     = (string) ( $this->data()['name'] ?? 'Tiers Pro' );
		$price    = $this->priceLabel();
		$features = $this->features();
		?>
		<aside class="tiers-card tiers-pro-aside" aria-labelledby="tiers-pro-aside-h">
			<p class="tiers-pro-aside__eyebrow"><?php echo esc_html( $name ); ?></p>
			<h2 id="tiers-pro-aside-h" class="tiers-pro-aside__heading"><?php esc_html_e( 'Unlock every PRO feature', 'plogins-tiers' ); ?></h2>
			<ul class="tiers-pro-aside__list">
				<?php foreach ( $features as $f ) : ?>
					<li>
						<span class="tiers-pro-aside__lock" aria-hidden="true"></span>
						<span><?php echo esc_html( $f['title'] ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>
			<a class="button button-primary button-hero tiers-pro-aside__cta" href="<?php echo esc_url( $this->url() ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Upgrade to PRO', 'plogins-tiers' ); ?>
			</a>
			<?php if ( '' !== $price ) : ?>
				<p class="tiers-pro-aside__price"><?php echo esc_html( $price ); ?> · <?php esc_html_e( 'one licence, every PRO feature', 'plogins-tiers' ); ?></p>
			<?php endif; ?>
		</aside>
		<?php
	}

	/**
	 * "What PRO adds" locked-card grid, appended after the settings form.
	 */
	public function cards(): void {
		if ( ! $this->enabled() ) {
			return;
		}

		$features = $this->features();
		$name     = (string) ( $this->data()['name'] ?? 'Tiers Pro' );
		?>
		<section class="tiers-pro-cards" aria-labelledby="tiers-pro-cards-h">
			<h2 id="tiers-pro-cards-h" class="tiers-pro-cards__title">
				<?php
				/* translators: %s: PRO edition name */
				printf( esc_html__( 'What %s adds', 'plogins-tiers' ), esc_html( $name ) );
				?>
			</h2>
			<div class="tiers-pro-cards__grid">
				<?php foreach ( $features as $f ) : ?>
					<article class="tiers-pro-card">
						<span class="tiers-pro-card__badge">PRO</span>
						<span class="tiers-pro-card__lock" aria-hidden="true"></span>
						<h3 class="tiers-pro-card__title"><?php echo esc_html( $f['title'] ); ?></h3>
						<?php if ( '' !== $f['desc'] ) : ?>
							<p class="tiers-pro-card__desc"><?php echo esc_html( $f['desc'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</section>
		<?php
	}
}
