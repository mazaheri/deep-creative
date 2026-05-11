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

define( 'DEEPSTUDIO_VERSION', '1.0.1' );

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

	// Tailwind CSS (CDN utility layer — not loaded on Video Brief which has its own CSS)
	if ( ! is_page_template( 'page-video-brief.php' ) ) {
		wp_enqueue_script(
			'tailwind',
			'https://cdn.tailwindcss.com',
			array(),
			null,
			false
		);
	}

	// Theme stylesheet
	wp_enqueue_style(
		'deepstudio-style',
		get_template_directory_uri() . '/assets/css/style.css',
		array( 'deepstudio-google-fonts' ),
		DEEPSTUDIO_VERSION
	);

	if ( is_page_template( 'page-video-brief.php' ) ) {
		// Video Brief: completely different design — own CSS + form JS, no particles
		wp_enqueue_style(
			'deepstudio-video-brief',
			get_template_directory_uri() . '/assets/css/video-brief.css',
			array(),
			DEEPSTUDIO_VERSION
		);

		wp_enqueue_script(
			'deepstudio-video-brief-form',
			get_template_directory_uri() . '/assets/js/video-brief-form.js',
			array(),
			DEEPSTUDIO_VERSION,
			true
		);

		wp_enqueue_script(
			'deepstudio-flicker',
			get_template_directory_uri() . '/assets/js/flicker.js',
			array(),
			DEEPSTUDIO_VERSION,
			true
		);
	} else {
		// Coming Soon neon form styles (particle page only)
		wp_enqueue_style(
			'deepstudio-coming-soon',
			get_template_directory_uri() . '/assets/css/coming-soon.css',
			array( 'deepstudio-style' ),
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

		// Thank-you screen + WhatsApp button after CF7 submission
		wp_enqueue_script(
			'deepstudio-form-thankyou',
			get_template_directory_uri() . '/assets/js/form-thankyou.js',
			array( 'deepstudio-particles' ),
			DEEPSTUDIO_VERSION,
			true
		);
	}
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

/* ------------------------------------------------------------------
   Customizer — Coming Soon settings (v7: every string is dynamic)
   ------------------------------------------------------------------ */
add_action( 'customize_register', function ( $wp_customize ) {

	$wp_customize->add_section( 'deepstudio_coming_soon', array(
		'title'    => __( 'Coming Soon', 'deepstudio' ),
		'priority' => 30,
	) );

	// Title
	$wp_customize->add_setting( 'deepstudio_cs_title', array(
		'default'           => 'Deep Creative Studio',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'deepstudio_cs_title', array(
		'label'   => __( 'Heading', 'deepstudio' ),
		'section' => 'deepstudio_coming_soon',
		'type'    => 'text',
	) );

	// Subtitle
	$wp_customize->add_setting( 'deepstudio_cs_subtitle', array(
		'default'           => 'Please fill the form, we will contact you very soon.',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'postMessage',
	) );
	$wp_customize->add_control( 'deepstudio_cs_subtitle', array(
		'label'   => __( 'Sub-heading', 'deepstudio' ),
		'section' => 'deepstudio_coming_soon',
		'type'    => 'text',
	) );

	// CF7 form ID
	$wp_customize->add_setting( 'deepstudio_cf7_id', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'deepstudio_cf7_id', array(
		'label'       => __( 'Contact Form 7 — Form ID', 'deepstudio' ),
		'description' => __( 'Enter the numeric ID of your CF7 form (found in CF7 form list).', 'deepstudio' ),
		'section'     => 'deepstudio_coming_soon',
		'type'        => 'number',
	) );

	/* ---- Video Brief section ---- */
	$wp_customize->add_section( 'deepstudio_video_brief', array(
		'title'    => __( 'Video Brief', 'deepstudio' ),
		'priority' => 31,
	) );

	// Showreel video URL
	$wp_customize->add_setting( 'deepstudio_vb_video_url', array(
		'default'           => 'http://deepcreative.studio/wp-content/uploads/videos/Hero%20Video%20Loop%20lQ.mp4',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( 'deepstudio_vb_video_url', array(
		'label'       => __( 'Showreel Video URL (.mp4)', 'deepstudio' ),
		'description' => __( 'Leave empty to use the bundled hero-loop.mp4 from the theme.', 'deepstudio' ),
		'section'     => 'deepstudio_video_brief',
		'type'        => 'url',
	) );

	// CF7 form ID
	$wp_customize->add_setting( 'deepstudio_vb_cf7_id', array(
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'deepstudio_vb_cf7_id', array(
		'label'       => __( 'Contact Form 7 — Form ID', 'deepstudio' ),
		'description' => __( 'Enter the numeric ID of the brief CF7 form.', 'deepstudio' ),
		'section'     => 'deepstudio_video_brief',
		'type'        => 'number',
	) );
} );

/* ------------------------------------------------------------------
   CF7 — always send results to the studio inbox
   ------------------------------------------------------------------ */
add_filter( 'wpcf7_mail_components', function ( $components ) {
	$components['recipient'] = 'iman@deepcreative.studio';
	return $components;
} );

/* ------------------------------------------------------------------
   CF7 — show success when Flamingo captures but SMTP is not configured
   ------------------------------------------------------------------ */
add_filter( 'wpcf7_ajax_json_echo', function ( $response ) {
	if ( isset( $response['status'] ) && 'mail_failed' === $response['status'] ) {
		$response['status']  = 'mail_sent';
		$response['message'] = esc_html__( 'Thank you! Your message has been received.', 'deepstudio' );
	}
	return $response;
} );

/* ------------------------------------------------------------------
   Demo importer (admin only)
   ------------------------------------------------------------------ */
if ( is_admin() ) {
	require get_template_directory() . '/inc/demo-importer.php';
}
