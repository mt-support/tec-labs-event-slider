<?php
/**
 * Plugin Name:       The Events Calendar Extension: Event Slider
 * Plugin URI:        __TRIBE_URL__
 * GitHub Plugin URI: https://github.com/mt-support/tec-labs-event-slider
 * Description:       Pure CSS event slider for The Events Calendar plugin
 * Version:           1.0.0
 * Author:            The Events Calendar
 * Author URI:        https://evnt.is/1971
 * License:           GPL version 3 or any later version
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       tec-labs-event-slider
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

/**
 * Define the base file that loaded the plugin for determining plugin path and other variables.
 *
 * @since 1.0.0
 *
 * @var string Base file that loaded the plugin.
 */
define( 'TRIBE_EXTENSION_EVENTSLIDER_FILE', __FILE__ );

/**
 * Register and load the service provider for loading the extension.
 *
 * @since 1.0.0
 */
function tribe_extension_event_slider() {
	// When we don't have autoloader from common we bail.
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	// Register the namespace so we can the plugin on the service provider registration.
	Tribe__Autoloader::instance()->register_prefix(
		'\\Tribe\\Extensions\\EventSlider\\',
		__DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Tec',
		'event-slider'
	);

	// Deactivates the plugin in case of the main class didn't autoload.
	if ( ! class_exists( '\Tribe\Extensions\EventSlider\Plugin' ) ) {
		tribe_transient_notice(
			'event-slider',
			'<p>' . esc_html__( 'Couldn\'t properly load "The Events Calendar Extension: Event Slider" the extension was deactivated.', 'tec-labs-event-slider' ) . '</p>',
			[],
			// 1 second after that make sure the transient is removed.
			1
		);

		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		deactivate_plugins( __FILE__, true );
		return;
	}

	tribe_register_provider( '\Tribe\Extensions\EventSlider\Plugin' );
}

// Loads after common is already properly loaded.
add_action( 'tribe_common_loaded', 'tribe_extension_event_slider' );

add_action('wp_enqueue_scripts', function() {
	wp_register_style('tec-events-slider', plugins_url('tec-labs-event-slider/src/css/tec-events-slider.css'));
	wp_enqueue_style('tec-events-slider');
});

// Add the shortcode [events_slider]
add_shortcode('events_slider', 'events_slider_shortcode');
	
function events_slider_shortcode($atts) {
	// Merge user attributes with default attributes
	$atts = shortcode_atts(array(
		'posts_per_page' => 5, // Default number of events to display
		'tag' => '', // Default tag to filter by
		'category' => '', // Default category to filter by
		'height' => '480', // Default height in pixels
		'width' => '640', // Default width in pixels
		'dots' => 'true',
		'color1' => '#000000',// Color 1, in hexadecimal
		'color2' => '#FFFFFF' // Color 2, in hexadecimal
	), $atts);
	
	$height = esc_attr($atts['height']);
	$width = esc_attr($atts['width']);
	$quantity = esc_attr($atts['posts_per_page']);

	// Define CSS content
	$color1 = esc_attr($atts['color1']);
	$color2 = esc_attr($atts['color2']);
	$output = '<style>
	ul.tec-events-slides{ height: ' . $height . 'px;}
	.tec-event-slide,.tec-event-slide-image{ height: ' . $height  . 'px;width: ' . $width . 'px;}
	.tec-event-slide-image img { height: ' . $height  . 'px;width: ' . $width . 'px;}
	.tec-event-slide-title{background-color: '. $color1 .'50;color:'. $color1 .';}
	.tec-event-slide-title h3{color:'. $color2 .';}
	.tec-event-carousel-controls {line-height: ' . $height . 'px;color: '. $color2 .';text-shadow: 2px 0 '. $color1 .', -2px 0 '. $color1 .', 0 2px '. $color1 .', 0 -2px '. $color1 .', 1px 1px '. $color1 .', -1px -1px '. $color1 .', 1px -1px '. $color1 .', -1px 1px '. $color1 .';}
	.tec-event-carousel-dots .tec-event-carousel-dot { background-color: '. $color2 .'; border: 1px solid '. $color1 .';}';	
	for ($j = 1; $j <= $quantity; $j++) {
	$output .= 'input#tec-event-img-' . $j . ':checked ~ .tec-event-carousel-dots label#tec-event-img-dot-' . $j .', input#tec-event-img-' . $j . ':hover ~ .tec-event-carousel-dots label#tec-event-img-dot-' . $j .'';
		//That's just to remove the last comma
		if($j < $quantity){
			$output .=', ';
		}
	}
	$output .='{opacity: 1;}</style>
	';
	
	// Initialize output
	$output .= '<div class="tec-events-slider">';

	// WP Query to get events
	$args = array(
		'post_type' => 'tribe_events',
		'posts_per_page' => $quantity,
		'tax_query' => array()
	);

	if (!empty($atts['tag'])) {
		$args['tax_query'][] = array(
			'taxonomy' => 'post_tag',
			'field' => 'slug',
			'terms' => $atts['tag']
		);
	}

	if (!empty($atts['category'])) {
		$args['tax_query'][] = array(
			'taxonomy' => 'tribe_events_cat',
			'field' => 'slug',
			'terms' => $atts['category']
		);
	}

	$query = new WP_Query($args);
	$i = 1;
	
	//Initialize the output
	$output .= '<div class="tec-event-slide""><ul class="tec-events-slides">';
	
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$thumbnail_id = get_post_thumbnail_id();
			$alt_text = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

			$output .= '<input type="radio" name="radio-buttons" id="tec-event-img-' . $i . '" ' . ($i === 1 ? 'checked' : '') . ' />';
			$output .= '<li class="tec-event-slide-container">';
			$output .= '<div class="tec-event-slide-image">';
			$output .= '<a href="' . get_permalink() . '">';
			if (has_post_thumbnail()){
				   $output .= '<img src="' . get_the_post_thumbnail_url() . '" alt="' . esc_attr($alt_text) . '" height="'. $height .'" width="'. $width .'">';
			} else {
				$upload = wp_upload_dir();
				$fallback_image = $upload['url'] . '/woocommerce-placeholder.png';
				$output .= '<img src="' . $fallback_image . '" alt="placeholder image" height="'. $height .'" width="'. $width .'">';
			}
			$output .= '</a>';
			$output .= '<div class="tec-event-slide-title"><a href="' . get_permalink() . '"><h3>' . get_the_title() . '</h3></a></div>';
			   $output .= '</div>';
			$output .= '<div class="tec-event-carousel-controls">';
			$output .= '<label for="tec-event-img-' . (($i - 1) === 0 ? $quantity : $i - 1) . '" class="tec-event-prev-slide"><span>‹</span></label>';
			$output .= '<label for="tec-event-img-' . (($i + 1) > $quantity ? 1 : $i + 1) . '" class="tec-event-next-slide"><span>›</span></label>';
			$output .= '</div>';
			$output .= '</li>';

			$i++;
		}
	} else {
		   $output .= '<p>No events found.</p>';
	   }

	wp_reset_postdata();

// Adding dots - bottom navigation
if ($atts['dots']=='true') {
	$output .= '<div class="tec-event-carousel-dots">';
	for ($j = 1; $j <= $quantity; $j++) {
		$output .= '<label for="tec-event-img-' . $j . '" class="tec-event-carousel-dot" id="tec-event-img-dot-' . $j . '"></label>';
	}
	$output .= '</div>';
}

$output .= '</ul></div>'; // Close .tec-events-slides and .tec-events-slider

return $output;
}
