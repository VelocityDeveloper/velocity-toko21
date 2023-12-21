<?php
/**
 * Enqueue child theme styles and scripts.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Load the parent style.css file
 *
 * @link http://codex.wordpress.org/Child_Themes
 */
if( ! function_exists( 'justg_child_enqueue_script_style') ) {
	function justg_child_enqueue_script_style() {
		// Dynamically get version number of the parent stylesheet (lets browsers re-cache your stylesheet when you update your theme)
		$parenthandle = 'parent-style'; 
        $theme = wp_get_theme();
		
		// Load the stylesheet
        wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css', 
            array(),  // if the parent theme code has a dependency, copy it to here
            $theme->parent()->get('Version')
        );
        
        $css_version = $theme->parent()->get('Version') . '.' . filemtime( get_stylesheet_directory() . '/css/custom.css' );
        // wp_enqueue_style( 'slick-style', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css', $css_version);
        // wp_enqueue_style( 'slick-style-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css', $css_version);
        wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri() . '/css/custom.css', 
            array(),  // if the parent theme code has a dependency, copy it to here
            $css_version
        );
        
        wp_enqueue_style( 'child-style', get_stylesheet_uri(),
            array( $parenthandle ),
            $theme->get('Version')
        );
        
        $js_version = $theme->parent()->get('Version') . '.' . filemtime( get_stylesheet_directory() . '/js/custom.js' );
        // wp_enqueue_script( 'slick-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js', array(), $js_version, true );
        wp_enqueue_script( 'justg-custom-scripts', get_stylesheet_directory_uri() . '/js/custom.js', array(), $js_version, true );

	}
	add_action( 'wp_enqueue_scripts', 'justg_child_enqueue_script_style', 20 );
}
