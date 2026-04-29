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

		<div class="social-links">
			<a href="https://instagram.com/deepstudio_creative/" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
					<circle cx="12" cy="12" r="4"/>
					<circle cx="17.5" cy="6.5" r="0.6" fill="currentColor" stroke="none"/>
				</svg>
			</a>
			<a href="https://www.linkedin.com/company/deepcreativestudio/" class="social-link" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
				<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
					<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/>
					<rect x="2" y="9" width="4" height="12"/>
					<circle cx="4" cy="4" r="2"/>
				</svg>
			</a>
		</div>

	</div>

</div>

<?php get_footer(); ?>
