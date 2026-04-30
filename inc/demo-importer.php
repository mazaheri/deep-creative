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
				<li><?php esc_html_e( 'Create the DeepStudio Brief contact form in CF7 (or use existing)', 'deepstudio' ); ?></li>
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
   Create or detect CF7 form and save ID to Customizer
   ------------------------------------------------------------------ */
function deepstudio_import_configure_cf7() {
	if ( ! class_exists( 'WPCF7' ) ) {
		return;
	}

	// CF7 [radio] tag parser chokes on $ and – characters, so we use
	// plain HTML radio buttons and sync the selection to a hidden CF7 field.
	$form_template = '<div class="cf7-section">
<p class="cf7-section-title"><span class="cf7-num">1</span> Brief</p>
<p class="cf7-hint">Tell us about your project (AI / CGI / campaign)<br />Include your idea, goal, or any references</p>
[textarea* project-brief placeholder "Describe your project..."]
</div>

<div class="cf7-divider"></div>

<div class="cf7-section">
<p class="cf7-section-title"><span class="cf7-num">2</span> Budget Range</p>
<div class="ds-budget-grid">
<label class="ds-budget-item"><input type="radio" name="ds_budget" value="$3,000 – $5,000"><span class="ds-budget-label">$3,000 – $5,000</span></label>
<label class="ds-budget-item"><input type="radio" name="ds_budget" value="$5,000 – $10,000"><span class="ds-budget-label">$5,000 – $10,000</span></label>
<label class="ds-budget-item"><input type="radio" name="ds_budget" value="$10,000 – $15,000"><span class="ds-budget-label">$10,000 – $15,000</span></label>
<label class="ds-budget-item"><input type="radio" name="ds_budget" value="$15,000+"><span class="ds-budget-label">$15,000+</span></label>
</div>
[hidden budget ""]
</div>

<div class="cf7-divider"></div>

<div class="cf7-section">
<p class="cf7-section-title"><span class="cf7-num">3</span> Contact Info</p>
[text* your-name placeholder "Name"]
[email* your-email placeholder "Email"]
[text* your-phone placeholder "Phone"]
[text your-company placeholder "Company Name (optional)"]
</div>

[submit "Submit Brief"]';

	// Find existing form — update its content rather than creating a duplicate
	$forms = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'orderby'        => 'ID',
		'order'          => 'ASC',
	) );

	if ( ! empty( $forms ) ) {
		$form_id = absint( $forms[0]->ID );
		update_post_meta( $form_id, '_form', $form_template );
		set_theme_mod( 'deepstudio_cf7_id', $form_id );
		return;
	}

	$form_id = wp_insert_post( array(
		'post_title'  => 'DeepStudio Brief',
		'post_type'   => 'wpcf7_contact_form',
		'post_status' => 'publish',
		'post_author' => get_current_user_id(),
	) );

	if ( is_wp_error( $form_id ) || ! $form_id ) {
		return;
	}

	update_post_meta( $form_id, '_form', $form_template );
	update_post_meta( $form_id, '_locale', get_locale() );

	$host = parse_url( home_url(), PHP_URL_HOST );
	update_post_meta( $form_id, '_mail', array(
		'active'        => true,
		'recipient'     => get_option( 'admin_email' ),
		'sender'        => get_bloginfo( 'name' ) . ' <wordpress@' . $host . '>',
		'subject'       => 'New Brief — [your-name]',
		'body'          => "Brief:\n[project-brief]\n\nBudget: [budget]\n\nName: [your-name]\nEmail: [your-email]\nPhone: [your-phone]\nCompany: [your-company]",
		'attachments'   => '',
		'use_html'      => false,
		'exclude_blank' => false,
	) );

	set_theme_mod( 'deepstudio_cf7_id', $form_id );
}
