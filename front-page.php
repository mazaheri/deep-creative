<?php
/**
 * Front page — Particle logo + Deep Creative Studio heading + Contact Form 7
 *
 * @package DeepStudio
 */

get_header();

$uc_title    = esc_html( get_theme_mod( 'deepstudio_cs_title',    'Deep Creative Studio'                                   ) );
$uc_subtitle = esc_html( get_theme_mod( 'deepstudio_cs_subtitle', 'Please fill the form, we will contact you very soon.'  ) );
$cf7_id      = absint(   get_theme_mod( 'deepstudio_cf7_id',      0                                                        ) );
?>

<div id="canvas-container">
	<canvas id="particleCanvas"></canvas>
</div>

<div id="ui-layer">

	<div class="logo-container" id="logo-anchor">
		<canvas id="logo-canvas"></canvas>
	</div>

	<div class="uc-section">

		<h1 class="uc-title"><?php echo $uc_title; ?></h1>
		<p  class="uc-subtitle"><?php echo $uc_subtitle; ?></p>

		<div class="uc-separator"></div>

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

</div>

<?php get_footer(); ?>
