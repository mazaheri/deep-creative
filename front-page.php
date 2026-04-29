<?php
/**
 * Coming Soon — Under Creative
 * Front page template with particle background + neon Contact Form 7.
 *
 * @package DeepStudio
 */

get_header();

$cs_title    = esc_html( get_theme_mod( 'deepstudio_cs_title',    'Coming Soon'     ) );
$cs_subtitle = esc_html( get_theme_mod( 'deepstudio_cs_subtitle', 'Under Creative'  ) );
$cf7_id      = absint(   get_theme_mod( 'deepstudio_cf7_id',      0                 ) );
?>

<div id="canvas-container">
	<canvas id="particleCanvas"></canvas>
</div>

<div id="coming-soon-layer">

	<div class="cs-logo">
		<canvas id="logo-canvas"></canvas>
	</div>

	<h1 class="cs-title"><?php echo $cs_title; ?></h1>
	<p  class="cs-subtitle"><?php echo $cs_subtitle; ?></p>

	<div class="cs-separator"></div>

	<div class="neon-form-wrap">
		<?php if ( class_exists( 'WPCF7' ) ) : ?>
			<?php
			$id = $cf7_id ? $cf7_id : 1;
			echo do_shortcode( '[contact-form-7 id="' . $id . '"]' );
			?>
		<?php else : ?>
			<p class="cs-plugin-notice">
				<?php esc_html_e( 'Please install and activate Contact Form 7.', 'deepstudio' ); ?>
			</p>
		<?php endif; ?>
	</div>

</div>

<?php get_footer(); ?>
