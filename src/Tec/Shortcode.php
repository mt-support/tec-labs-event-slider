<?php
/**
 * Plugin Class.
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider
 */

namespace TEC\Extensions\EventSlider;

use \WP_Query;

/**
 * Class Shortcode
 *
 * @since 1.0.0
 *
 * @package TEC\Extensions\EventSlider
 */
class Shortcode {

	public function register_shortcode() {
		add_shortcode( 'events_slider', 'events_slider_shortcode' );
	}

	public function events_slider_shortcode($atts) {
		// Merge user attributes with default attributes
		$atts = shortcode_atts(
			[
				'posts_per_page' => 5, // Default number of events to display
				'tag' => '', // Default tag to filter by
				'category' => '', // Default category to filter by
				'height' => '480', // Default height in pixels
				'width' => '640', // Default width in pixels
				'dots' => 'true',
				'color1' => '#000000',// Color 1, in hexadecimal
				'color2' => '#FFFFFF' // Color 2, in hexadecimal
			],
			$atts
		);

		$height = esc_attr($atts['height']);
		$width = esc_attr($atts['width']);
		$quantity = esc_attr($atts['posts_per_page']);

		$output = '<style>';
		$output .= '
			:root{
				--tec-labs-event-slider-height: ' . esc_attr( $atts['height'] ) . 'px;
				--tec-labs-event-slider-width: ' . esc_attr( $atts['width'] ) . 'px;
				--tec-labs-event-slider-color-1: ' . esc_attr( $atts['color1'] ) . ';
				--tec-labs-event-slider-color-background: ' . esc_attr( $atts['color1'] ) . '50;
				--tec-labs-event-slider-color-2: ' . esc_attr( $atts['color2'] ) . ';
			}
		';
		for ($j = 1; $j <= $quantity; $j++) {
		$output .= 'input#tec-event-img-' . $j . ':checked ~ .tec-event-carousel-dots label#tec-event-img-dot-' . $j .', input#tec-event-img-' . $j . ':hover ~ .tec-event-carousel-dots label#tec-event-img-dot-' . $j .'';
			//That's just to remove the last comma
			if($j < $quantity){
				$output .=', ';
			}
		}
		$output .='{opacity: 1;}';
		$output .='</style>';

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
		$output .= '<div class="tec-event-slide"><ul class="tec-events-slides">';

		if ( $query->have_posts() ) {
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
					$fallback_image = plugins_url('tec-labs-event-slider/src/resources/img/placeholder.png');
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
}
