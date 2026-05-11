<?php
/**
 * DeepStudio Demo Importer
 *
 * Each import action deletes any existing page of that name and recreates it
 * fresh from the current template. The CF7 form is updated in place (not
 * deleted) so that submission history stored in Flamingo is preserved.
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
   Helper — build a nonce-secured import URL
   ------------------------------------------------------------------ */
function deepstudio_import_url( $action ) {
	return wp_nonce_url(
		admin_url( 'themes.php?page=deepstudio-demo-importer&action=' . $action ),
		'deepstudio_import'
	);
}

/* ------------------------------------------------------------------
   Helper — force-delete every page (any status) with a given title
   ------------------------------------------------------------------ */
function deepstudio_purge_pages( $title ) {
	$posts = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => array( 'publish', 'draft', 'trash', 'private', 'pending', 'future' ),
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );

	foreach ( $posts as $post ) {
		if ( $post->post_title === $title ) {
			wp_delete_post( $post->ID, true );
		}
	}
}

/* ------------------------------------------------------------------
   Admin page output
   ------------------------------------------------------------------ */
function deepstudio_demo_importer_page() {
	$imported    = isset( $_GET['imported'] ) ? sanitize_key( $_GET['imported'] ) : '';
	$confirm_cs  = esc_js( __( 'This will delete and recreate the Coming Soon page. Continue?', 'deepstudio' ) );
	$confirm_vb  = esc_js( __( 'This will delete and recreate the Video Brief page. Continue?', 'deepstudio' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'DeepStudio — Import Demo', 'deepstudio' ); ?></h1>

		<?php if ( $imported === '1' ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Coming Soon page re-imported successfully and set as the front page.', 'deepstudio' ); ?></p>
			</div>
		<?php elseif ( $imported === '2' ) : ?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e( 'Video Brief page re-imported. Set your video URL at Appearance → Customize → Video Brief.', 'deepstudio' ); ?></p>
			</div>
		<?php endif; ?>

		<p style="color:#666;margin-top:12px;">
			<?php esc_html_e( 'Each import button deletes the existing page and recreates it fresh. The CF7 form is updated in place so submission history is not lost.', 'deepstudio' ); ?>
		</p>

		<div style="display:flex;gap:24px;flex-wrap:wrap;margin-top:20px;">

			<!-- Card 1: Coming Soon -->
			<div class="card" style="flex:1;min-width:300px;max-width:520px;padding:20px 24px;">
				<h2><?php esc_html_e( 'Coming Soon — Particle Canvas', 'deepstudio' ); ?></h2>
				<p><?php esc_html_e( 'Each click will:', 'deepstudio' ); ?></p>
				<ul style="list-style:disc;margin-left:20px;line-height:2">
					<li><?php esc_html_e( 'Delete any existing "Coming Soon" page and recreate it', 'deepstudio' ); ?></li>
					<li><?php esc_html_e( 'Set it as the static front page', 'deepstudio' ); ?></li>
					<li><?php esc_html_e( 'Update the CF7 form fields to the latest template', 'deepstudio' ); ?></li>
				</ul>
				<p style="margin-top:12px;">
					<strong><?php esc_html_e( 'After import:', 'deepstudio' ); ?></strong>
					<?php esc_html_e( 'Appearance → Customize → Coming Soon to edit heading, sub-heading, and CF7 form ID.', 'deepstudio' ); ?>
				</p>
				<p style="margin-top:20px;">
					<a href="<?php echo esc_url( deepstudio_import_url( 'import' ) ); ?>"
					   class="button button-primary button-hero"
					   onclick="return confirm('<?php echo $confirm_cs; ?>');">
						<?php esc_html_e( 'Import / Reimport Coming Soon', 'deepstudio' ); ?>
					</a>
				</p>
			</div>

			<!-- Card 2: Video Brief -->
			<div class="card" style="flex:1;min-width:300px;max-width:520px;padding:20px 24px;">
				<h2><?php esc_html_e( 'Video Brief — Looping Video + Form', 'deepstudio' ); ?></h2>
				<p><?php esc_html_e( 'Each click will:', 'deepstudio' ); ?></p>
				<ul style="list-style:disc;margin-left:20px;line-height:2">
					<li><?php esc_html_e( 'Delete any existing "Video Brief" page and recreate it', 'deepstudio' ); ?></li>
					<li><?php esc_html_e( 'Assign the Video Brief template to the new page', 'deepstudio' ); ?></li>
					<li><?php esc_html_e( 'Update the CF7 form fields to the latest template', 'deepstudio' ); ?></li>
				</ul>
				<p style="margin-top:12px;">
					<strong><?php esc_html_e( 'After import:', 'deepstudio' ); ?></strong>
					<?php esc_html_e( 'Appearance → Customize → Video Brief to set the showreel video URL and CF7 form ID.', 'deepstudio' ); ?>
				</p>
				<p style="margin-top:20px;">
					<a href="<?php echo esc_url( deepstudio_import_url( 'import_video' ) ); ?>"
					   class="button button-primary button-hero"
					   onclick="return confirm('<?php echo $confirm_vb; ?>');">
						<?php esc_html_e( 'Import / Reimport Video Brief', 'deepstudio' ); ?>
					</a>
				</p>
			</div>

		</div>
	</div>
	<?php
}

/* ------------------------------------------------------------------
   Process import on admin_init
   ------------------------------------------------------------------ */
add_action( 'admin_init', function () {
	if (
		! isset( $_GET['page'] ) || $_GET['page'] !== 'deepstudio-demo-importer' ||
		! isset( $_GET['action'] )
	) {
		return;
	}

	$action = sanitize_key( $_GET['action'] );

	if ( ! in_array( $action, array( 'import', 'import_video' ), true ) ) {
		return;
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Permission denied.', 'deepstudio' ) );
	}

	check_admin_referer( 'deepstudio_import' );

	if ( $action === 'import' ) {
		deepstudio_import_coming_soon_page();
		deepstudio_import_configure_cf7();
		wp_safe_redirect( admin_url( 'themes.php?page=deepstudio-demo-importer&imported=1' ) );
	} else {
		deepstudio_import_video_brief_page();
		deepstudio_import_configure_cf7_for_vb();
		wp_safe_redirect( admin_url( 'themes.php?page=deepstudio-demo-importer&imported=2' ) );
	}

	exit;
} );

/* ------------------------------------------------------------------
   Coming Soon: delete existing page, recreate, set as front page
   ------------------------------------------------------------------ */
function deepstudio_import_coming_soon_page() {
	// Wipe any previous version (publish, draft, trash, etc.)
	deepstudio_purge_pages( 'Coming Soon' );

	$page_id = wp_insert_post( array(
		'post_title'   => 'Coming Soon',
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => get_current_user_id(),
	) );

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	// front-page.php auto-loads for the static front page — no template meta needed
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $page_id );
}

/* ------------------------------------------------------------------
   CF7 form: update in place (preserves submission history)
   Creates the form if it doesn't exist yet.
   ------------------------------------------------------------------ */
function deepstudio_import_configure_cf7() {
	if ( ! class_exists( 'WPCF7' ) ) {
		return;
	}

	$form_template = '<div class="field">
<label>Project idea</label>
[textarea* project-idea placeholder "Describe the campaign, product, launch, reference, location, or visual idea..."]
</div>

<div class="brief-grid">
<div class="field">
<label>Service</label>
[select* service "AI Commercial Production" "CGI / FOOH Activation" "Premium Video Production" "Hybrid AI + CGI + VFX" "Not sure yet"]
</div>
<div class="field">
<label>Budget</label>
[select* budget "$3,000 – $5,000" "$5,000 – $10,000" "$10,000 – $15,000" "$15,000+" "Not confirmed yet"]
</div>
</div>

<div class="brief-grid">
<div class="field">
<label>Name</label>
[text* your-name placeholder "Your name"]
</div>
<div class="field">
<label>Company</label>
[text your-company placeholder "Company name"]
</div>
</div>

<div class="brief-grid">
<div class="field">
<label>Email</label>
[email* your-email placeholder "name@company.com"]
</div>
<div class="field">
<label>WhatsApp</label>
[tel* your-phone placeholder "+971..."]
</div>
</div>

<div class="submit-row">
[submit "Submit Brief"]
</div>

<div class="form-note">For faster response, send references or a short voice note through WhatsApp.</div>';

	$mail_body = "Project idea:\n[project-idea]\n\nService: [service]\nBudget: [budget]\n\nName: [your-name]\nEmail: [your-email]\nWhatsApp: [your-phone]\nCompany: [your-company]";
	$host      = parse_url( home_url(), PHP_URL_HOST );

	// Find the canonical DeepStudio Brief form
	$forms = get_posts( array(
		'post_type'      => 'wpcf7_contact_form',
		'post_status'    => 'publish',
		'posts_per_page' => 1,
		'orderby'        => 'ID',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	) );

	if ( ! empty( $forms ) ) {
		// Update existing — keeps Flamingo submission history intact
		$form_id = absint( $forms[0]->ID );
		update_post_meta( $form_id, '_form', $form_template );
		update_post_meta( $form_id, '_mail', array(
			'active'        => true,
			'recipient'     => get_option( 'admin_email' ),
			'sender'        => get_bloginfo( 'name' ) . ' <wordpress@' . $host . '>',
			'subject'       => 'New Brief — [your-name]',
			'body'          => $mail_body,
			'attachments'   => '',
			'use_html'      => false,
			'exclude_blank' => false,
		) );
		set_theme_mod( 'deepstudio_cf7_id', $form_id );
		set_theme_mod( 'deepstudio_vb_cf7_id', $form_id );
		return;
	}

	// First-time install — create the form
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
	update_post_meta( $form_id, '_mail', array(
		'active'        => true,
		'recipient'     => get_option( 'admin_email' ),
		'sender'        => get_bloginfo( 'name' ) . ' <wordpress@' . $host . '>',
		'subject'       => 'New Brief — [your-name]',
		'body'          => $mail_body,
		'attachments'   => '',
		'use_html'      => false,
		'exclude_blank' => false,
	) );

	set_theme_mod( 'deepstudio_cf7_id', $form_id );
	set_theme_mod( 'deepstudio_vb_cf7_id', $form_id );
}

/* ------------------------------------------------------------------
   Video Brief: delete existing page, recreate with template assigned
   ------------------------------------------------------------------ */
function deepstudio_import_video_brief_page() {
	deepstudio_purge_pages( 'Video Brief' );

	$page_id = wp_insert_post( array(
		'post_title'   => 'Video Brief',
		'post_content' => '',
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => get_current_user_id(),
	) );

	if ( is_wp_error( $page_id ) || ! $page_id ) {
		return;
	}

	update_post_meta( $page_id, '_wp_page_template', 'page-video-brief.php' );
}

/* ------------------------------------------------------------------
   CF7 for Video Brief — delegates to the shared configure function
   ------------------------------------------------------------------ */
function deepstudio_import_configure_cf7_for_vb() {
	deepstudio_import_configure_cf7();
}
