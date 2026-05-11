<?php
/**
 * Template Name: Video Brief
 * Template Post Type: page
 *
 * Converted from deep-creative-exact-neon-wall-landing-v6.html
 * Hero copy · Phone-frame video reel · Brief form card · Services strip
 *
 * @package DeepStudio
 */

get_header();

$cf7_id    = absint( get_theme_mod( 'deepstudio_vb_cf7_id',    0 ) );
$video_url = esc_url( get_theme_mod( 'deepstudio_vb_video_url', 'https://deepcreative.studio/wp-content/uploads/videos/Hero%20Video%20Loop%20lQ.mp4' ) );
$form_id   = $cf7_id ? $cf7_id : 1;
?>

<div class="hero-wall"></div>
<div class="grain"></div>

<div class="page">

  <header>
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <strong><?php bloginfo( 'name' ); ?></strong>
      <span>AI + CGI Powered</span>
    </a>

    <nav class="nav">
      <a href="#services">What we do</a>
      
      <a class="button" href="https://wa.me/971563955262">WhatsApp</a>
    </nav>
  </header>

  <main class="hero">

    <!-- ── Copy ── -->
    <section class="copy">
      <div class="kicker">Premium AI · CGI · VFX Campaigns</div>

      <h1>
        Premium visuals.<br>
        <span class="gradient">Built to make brands impossible to ignore.</span>
      </h1>

      <p class="lead">
        Deep Creative Studio creates high-end AI, CGI, VFX and hybrid video campaigns for brands, agencies and government-level projects that need standout visual execution.
      </p>

      <div class="hero-actions">
        <a class="button primary" href="#brief">Submit Your Brief</a>
        <a class="button" href="#showreel">View Showreel</a>
      </div>

      
    </section>

    <!-- ── Phone-frame showreel ── -->
    <section class="vertical-wrap" id="showreel">
      <div class="reel-frame">
        <div class="notch"></div>

        <div class="reel">
          <video autoplay muted loop playsinline>
            <source src="<?php echo $video_url; ?>" type="video/mp4">
          </video>

          <div class="reel-overlay">
            <div class="reel-top">
              <div class="label">Deep Creative Studio</div>
            </div>

            <div class="reel-bottom">
              <h2>Selected Works</h2>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ── Brief card ── -->
    <section class="brief-card" id="brief">
      <div class="brief-head">
        <h2>Start with a brief</h2>
        <p>Tell us what you want to create. We'll review your idea and contact you with the right creative direction, scope and next steps.</p>
      </div>

      <?php if ( class_exists( 'WPCF7' ) ) : ?>
        <?php echo do_shortcode( '[contact-form-7 id="' . $form_id . '"]' ); ?>
      <?php else : ?>
        <p style="color:#a4b6c1;font-size:14px;">
          <?php esc_html_e( 'Please install and activate Contact Form 7.', 'deepstudio' ); ?>
        </p>
      <?php endif; ?>
    </section>

  </main>

  <!-- ── Services strip ── -->
  <section class="services" id="services">
    <div class="service">
      <strong>AI Campaign Films</strong>
      <span>Hyper-real campaign visuals designed for modern launches and social-first storytelling.</span>
    </div>

    <div class="service">
      <strong>CGI / FOOH</strong>
      <span>Large-scale visual illusions and scroll-stopping activations built for attention.</span>
    </div>

    <div class="service">
      <strong>Hybrid Production</strong>
      <span>Real footage, CGI, AI, VFX, compositing, color and sound in one controlled workflow.</span>
    </div>

    <div class="service">
      <strong>Creative Direction</strong>
      <span>Concept, references, storyboard direction and production planning before execution.</span>
    </div>
  </section>

</div><!-- .page -->

<a class="button whatsapp" href="https://wa.me/971563955262">Chat on WhatsApp</a>

<?php get_footer(); ?>
