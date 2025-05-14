<?php
/**
 * Handles registering all Assets for the Plugin.
 *
 * To remove an Asset you can use the global assets handler:
 *
 * ```php
 *  tribe( 'assets' )->remove( 'asset-name' );
 * ```
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider
 */

namespace TEC\Extensions\EventSlider;

use TEC\Common\Contracts\Service_Provider;

/**
 * Register Assets.
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider
 */
class Assets extends Service_Provider {
	/**
	 * Flag to track if autorotation is needed on the current page
	 */
	protected static $needs_autorotation = false;

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_slider.assets', $this );
	}

	/**
	 * Enable autorotation flag
	 */
	public static function enable_autorotation() {
		self::$needs_autorotation = true;
	}

	/**
	 * Check if autorotation is needed
	 */
	public static function needs_autorotation() {
		return self::$needs_autorotation;
	}

	/**
	 * Load assets with conditional JavaScript loading
	 */
	public function load_assets() {
		// Always load CSS
		wp_enqueue_style(
			'tec-events-slider',
			plugins_url('tec-labs-event-slider/src/css/tec-events-slider.css'),
			[],
			Plugin::VERSION
		);

		// Conditionally load JS only if autorotation is needed
		if (self::needs_autorotation()) {
			wp_enqueue_script(
				'tec-events-slider',
				plugins_url('tec-labs-event-slider/src/js/tec-events-slider.js'),
				[],
				Plugin::VERSION,
				true
			);
		}
	}
}
