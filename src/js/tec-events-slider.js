/**
 * TEC Events Slider JavaScript
 *
 * Handles auto-rotation functionality for the Events Slider.
 */

// Create namespace to avoid polluting the global namespace
window.TEC = window.TEC || {};
window.TEC.EventSlider = window.TEC.EventSlider || {};

( function( window, document, $, namespace ) {
    'use strict';

    // Reusable selector variables
    const selectors = {
        slider: '.tec-events-slider',
        sliderWithAutorotate: '.tec-events-slider[data-autorotate]',
        radioButtons: "input[type='radio']"
    };

    // Default configuration
    const defaults = {
        autorotateAttribute: 'data-autorotate',
        pauseOnHover: true,
        startOnLoad: true
    };

    // Configuration object that can be overridden
    let config = { ...defaults };

    /**
     * Creates and manages a slider rotation.
     *
     * @param {HTMLElement} container - The slider container element.
     * @param {number} interval - Rotation interval in milliseconds.
     * @param {Object} options - Optional configuration overrides.
     * @return {Object} - Public methods for the slider.
     */
    const createSliderRotation = ( container, interval, options = {} ) => {
        // Merge default config with options
        const sliderConfig = { ...config, ...options };

        // Bail early if no container or invalid interval
        if ( ! container || interval <= 0 ) {
            return null;
        }

        const radioButtons = container.querySelectorAll( selectors.radioButtons );
        
        // Bail if no radio buttons found
        if ( ! radioButtons.length ) {
            return null;
        }

        let currentSlide = 0;
        let rotationTimer = null;

        // Find the initially checked radio button
        radioButtons.forEach( ( radio, index ) => {
            if ( radio.checked ) {
                currentSlide = index;
            }
        } );

        /**
         * Rotates to the next slide.
         */
        const rotateSlide = () => {
            currentSlide = ( currentSlide + 1 ) % radioButtons.length;
            radioButtons[ currentSlide ].checked = true;
        };

        /**
         * Starts the rotation timer.
         */
        const startRotation = () => {
            // Clear any existing timer first
            stopRotation();
            rotationTimer = setInterval( rotateSlide, interval );
        };

        /**
         * Stops the rotation timer.
         */
        const stopRotation = () => {
            if ( rotationTimer ) {
                clearInterval( rotationTimer );
                rotationTimer = null;
            }
        };

        /**
         * Sets up hover handlers to pause/resume rotation.
         */
        const setupHoverHandlers = () => {
            if ( sliderConfig.pauseOnHover ) {
                container.addEventListener( 'mouseenter', stopRotation );
                container.addEventListener( 'mouseleave', startRotation );
            }
        };

        // Initialize rotation
        if ( sliderConfig.startOnLoad ) {
            startRotation();
            setupHoverHandlers();
        }

        // Public methods
        return {
            start: startRotation,
            stop: stopRotation,
            rotate: rotateSlide,
            getCurrentSlide: () => currentSlide
        };
    };

    /**
     * Initialize all sliders when DOM is ready.
     */
    const init = ( customConfig = {} ) => {
        // Apply any custom configuration
        config = { ...config, ...customConfig };

        const sliders = document.querySelectorAll( selectors.sliderWithAutorotate );
        
        if ( ! sliders.length ) {
            return [];
        }

        const instances = [];

        sliders.forEach( slider => {
            const intervalInSeconds = parseInt( slider.getAttribute( config.autorotateAttribute ), 10 );
            
            // Bail if autorotate is not a positive number
            if ( ! intervalInSeconds || intervalInSeconds <= 0 ) {
                return;
            }

            // Convert seconds to milliseconds
            const intervalInMs = intervalInSeconds * 1000;
            
            // Initialize slider rotation
            const instance = createSliderRotation( slider, intervalInMs );
            if ( instance ) {
                instances.push( instance );
            }
        } );

        return instances;
    };

    /**
     * Updates the configuration.
     *
     * @param {Object} newConfig - New configuration to apply.
     */
    const configure = ( newConfig = {} ) => {
        config = { ...config, ...newConfig };
        return config;
    };

    /**
     * Get current configuration.
     *
     * @return {Object} Current configuration.
     */
    const getConfig = () => {
        return { ...config };
    };

    // Expose public API
    namespace.createSlider = createSliderRotation;
    namespace.init = init;
    namespace.configure = configure;
    namespace.getConfig = getConfig;
    namespace.selectors = selectors;

    // Auto-initialize when DOM is ready
    document.addEventListener( 'DOMContentLoaded', init );

} )( window, document, jQuery, window.TEC.EventSlider ); 