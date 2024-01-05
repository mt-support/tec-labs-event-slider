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
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_slider.assets', $this );
	}

	public function load_assets() {
		wp_enqueue_style(
			'tec-events-slider',
			plugins_url('tec-labs-event-slider/src/css/tec-events-slider.css'),
			[],
			Plugin::VERSION
		);
	}
}
