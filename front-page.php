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

		<a href="https://wa.me/971563955262" class="ds-wa-btn ds-wa-below-form" target="_blank" rel="noopener noreferrer">
			<svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
			<?php esc_html_e( 'Chat on WhatsApp', 'deepstudio' ); ?>
		</a>

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
