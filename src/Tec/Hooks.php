<?php
/**
 * Handles hooking all the actions and filters used by the module.
 *
 * To remove a filter:
 * ```php
 *  remove_filter( 'some_filter', [ tribe( TEC\Extensions\EventSlider\Hooks::class ), 'some_filtering_method' ] );
 *  remove_filter( 'some_filter', [ tribe( 'extension.event_slider.hooks' ), 'some_filtering_method' ] );
 * ```
 *
 * To remove an action:
 * ```php
 *  remove_action( 'some_action', [ tribe( TEC\Extensions\EventSlider\Hooks::class ), 'some_method' ] );
 *  remove_action( 'some_action', [ tribe( 'extension.event_slider.hooks' ), 'some_method' ] );
 * ```
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider;
 */

namespace TEC\Extensions\EventSlider;

use Tribe__Main as Common;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Hooks.
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider;
 */
class Hooks extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->container->singleton( static::class, $this );
		$this->container->singleton( 'extension.event_slider.hooks', $this );

		$this->register_shortcode();

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds the actions required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_actions() {
		add_action( 'tribe_load_text_domains', [ $this, 'load_text_domains' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'load_assets' ] );
	}

	/**
	 * Adds the filters required by the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function add_filters() {}

	/**
	 * Load text domain for localization of the plugin.
	 *
	 * @since 1.0.0
	 */
	public function load_text_domains() {
		$mopath = tribe( Plugin::class )->plugin_dir . 'lang/';
		$domain = 'tec-labs-event-slider';

		// This will load `wp-content/languages/plugins` files first.
		Common::instance()->load_text_domain( $domain, $mopath );
	}

	public function register_shortcode() {
		$this->container->make( Shortcode::class )->register_shortcode();
	}

	public function load_assets() {
		$this->container->make( Assets::class )->load_assets();
	}
}
