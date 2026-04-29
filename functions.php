<?php
/**
 * DeepStudio theme functions
 *
 * @package DeepStudio
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DEEPSTUDIO_VERSION', '1.0.0' );

function deepstudio_setup() {
	load_theme_textdomain( 'deepstudio', get_template_directory() . '/languages' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array(
		'height'      => 250,
		'width'       => 250,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );
}
add_action( 'after_setup_theme', 'deepstudio_setup' );

function deepstudio_enqueue_assets() {
	// Google Fonts — Inter
	wp_enqueue_style(
		'deepstudio-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap',
		array(),
		null
	);

	// Tailwind CSS (CDN utility layer used by original template)
	wp_enqueue_script(
		'tailwind',
		'https://cdn.tailwindcss.com',
		array(),
		null,
		false // must load in <head> so utilities are available before paint
	);

	// Theme stylesheet
	wp_enqueue_style(
		'deepstudio-style',
		get_template_directory_uri() . '/assets/css/style.css',
		array( 'deepstudio-google-fonts' ),
		DEEPSTUDIO_VERSION
	);

	// Particle animation script (loaded in footer so DOM is ready)
	wp_enqueue_script(
		'deepstudio-particles',
		get_template_directory_uri() . '/assets/js/particles.js',
		array(),
		DEEPSTUDIO_VERSION,
		true
	);

	// Pass the logo URL to the JS so it resolves correctly in WordPress
	wp_localize_script( 'deepstudio-particles', 'deepstudioData', array(
		'logoSrc' => esc_url( get_template_directory_uri() . '/assets/images/deep-logo.png' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'deepstudio_enqueue_assets' );

// Remove query strings from Tailwind CDN script tag (it has none, but keep for safety)
function deepstudio_remove_wp_version_strings( $src ) {
	parse_str( parse_url( $src, PHP_URL_QUERY ), $query );
	global $wp_version;
	if ( ! empty( $query['ver'] ) && $query['ver'] === $wp_version ) {
		$src = remove_query_arg( 'ver', $src );
	}
	return $src;
}
add_filter( 'style_loader_src', 'deepstudio_remove_wp_version_strings' );
add_filter( 'script_loader_src', 'deepstudio_remove_wp_version_strings' );

// Remove unnecessary head items
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
