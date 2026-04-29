<?php
/**
 * DeepStudio Demo Importer
 *
 * Creates the Coming Soon page, sets it as the static front page,
 * and wires up Contact Form 7 if active.
 *
 * Access via: Appearance → Import Demo
 *
 * @package DeepStudio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------
   Admin menu
   ------------------------------------------------------------------ */
add_action( 'admin_menu', function () {
	add_theme_page(
		__( 'DeepStudio Demo Importer', 'deepstudio' ),
		__( 'Import Demo', 'deepstudio' ),
		'manage_options',
		'deepstudio-demo-importer',
		'deepstudio_demo_importer_page'
	);
} );

/* ------------------------------------------------------------------
   Admin page output
   ------------------------------------------------------------------ */
function deepstudio_demo_importer_page() {
	$imported = isset( $_GET['imported'] ) && $_GET['imported'] === '1';
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'DeepStudio — Import Demo', 'deepstudio' ); ?></h1>

		<?php if ( $imported ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Demo imported successfully! Your Coming Soon page is now live.', 'deepstudio' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="card" style="max-width:700px;margin-top:20px;padding:20px 24px;">
			<h2><?php esc_html_e( 'Coming Soon — Under Creative', 'deepstudio' ); ?></h2>
			<p><?php esc_html_e( 'Clicking Import will:', 'deepstudio' ); ?></p>
			<ul style="list-style:disc;margin-left:20px;line-height:2">
				<li><?php esc_html_e( 'Create a "Coming Soon" page (if it doesn\'t exist)', 'deepstudio' ); ?></li>
				<li><?php esc_html_e( 'Set that page as the static front page', 'deepstudio' ); ?></li>
				<li><?php esc_html_e( 'Detect Contact Form 7 and save the first available form ID to the Customizer', 'deepstudio' ); ?></li>
			</ul>

			<p style="margin-top:16px;">
				<strong><?php esc_html_e( 'After import:', 'deepstudio' ); ?></strong>
				<?php esc_html_e( 'Go to Appearance → Customize → Coming Soon to edit the heading, sub-heading, and CF7 form ID.', 'deepstudio' ); ?>
			</p>

			<p style="margin-top:20px;">
				<a href="<?php echo esc_url( wp_nonce_url(
					admin_url( 'themes.php?page=deepstudio-demo-importer&action=import' ),
					'deepstudio_import'
				) ); ?>"
				   class="button button-primary button-hero"
				   onclick="return confirm('<?php esc_attr_e( 'Run the demo import now?', 'deepstudio' ); ?>');">
					<?php esc_html_e( 'Import Demo Content', 'deepstudio' ); ?>
				</a>
			</p>
		</div>
	</div>
	<?php
}

/* ------------------------------------------------------------------
   Process import on admin_init
   ------------------------------------------------------------------ */
add_action( 'admin_init', function () {
	if (
		! isset( $_GET['page'] )   || $_GET['page']   !== 'deepstudio-demo-importer' ||
		! isset( $_GET['action'] ) || $_GET['action'] !== 'import'
	) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'deepstudio' ) );
	}

	check_admin_referer( 'deepstudio_import' );

	deepstudio_import_coming_soon_page();
	deepstudio_import_configure_cf7();

	wp_safe_redirect( admin_url( 'themes.php?page=deepstudio-demo-importer&imported=1' ) );
	exit;
} );

/* ------------------------------------------------------------------
   Create Coming Soon page + set as front page
   ------------------------------------------------------------------ */
function deepstudio_import_coming_soon_page() {
	$existing = get_page_by_title( 'Coming Soon' );

	if ( $existing ) {
		$page_id = $existing->ID;
	} else {
		$page_id = wp_insert_post( array(
			'post_title'   => 'Coming Soon',
			'post_content' => '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_author'  => get_current_user_id(),
		) );
	}

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	// Set as static front page (front-page.php auto-loads, no template meta needed)
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );
}

/* ------------------------------------------------------------------
   Detect first CF7 form and save ID to Customizer
   ------------------------------------------------------------------ */
function deepstudio_import_configure_cf7() {
	if ( ! class_exists( 'WPCF7' ) ) {
		return;
	}

	// Find the first published CF7 form
	$forms = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	) );

	if ( empty( $forms ) ) {
		return;
	}

	// Save the form ID so the Customizer / front-page.php picks it up
	set_theme_mod( 'deepstudio_cf7_id', absint( $forms[0]->ID ) );
}
