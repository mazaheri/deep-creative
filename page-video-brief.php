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

      <?php
      $cc_list = [
        ['+971','UAE'],['+966','Saudi Arabia'],['+965','Kuwait'],
        ['+974','Qatar'],['+973','Bahrain'],['+968','Oman'],
        ['+962','Jordan'],['+961','Lebanon'],['+20','Egypt'],
        ['+90','Turkey'],['+91','India'],['+92','Pakistan'],
        ['+1','US / Canada'],['+44','UK'],['+33','France'],
        ['+49','Germany'],['+61','Australia'],
      ];
      ?>

      <form id="brief-form" novalidate>
        <?php wp_nonce_field( 'brief_submit', 'brief_nonce' ); ?>

        <div class="brief-grid">
          <div class="field">
            <label for="bf-name">Name <span class="req">*</span></label>
            <input id="bf-name" name="bf_name" type="text" required placeholder="Your name">
          </div>
          <div class="field">
            <label for="bf-company">Company <span class="req">*</span></label>
            <input id="bf-company" name="bf_company" type="text" required placeholder="Company name">
          </div>
        </div>

        <div class="field">
          <label for="bf-email">Email <span class="req">*</span></label>
          <input id="bf-email" name="bf_email" type="email" required placeholder="name@company.com">
        </div>

        <div class="field">
          <label>Contact Number <span class="req">*</span></label>
          <div class="phone-field-wrap">
            <div class="phone-cc-wrap">
              <select class="phone-cc" name="bf_phone_cc" aria-label="Country code">
                <?php foreach ( $cc_list as $c ) : ?>
                  <option value="<?php echo esc_attr( $c[0] ); ?>"<?php selected( $c[0], '+971' ); ?>>
                    <?php echo esc_html( $c[0] . '  ' . $c[1] ); ?>
                  </option>
                <?php endforeach; ?>
                <option value="other">Other…</option>
              </select>
              <input type="text" class="phone-cc-custom" name="bf_phone_cc_custom" placeholder="+XX" style="display:none" aria-label="Enter country code">
            </div>
            <input type="tel" id="bf-phone" name="bf_phone" required placeholder="50 123 4567" autocomplete="tel-national">
          </div>
        </div>

        <div class="brief-grid">
          <div class="field">
            <label for="bf-service">Service <span class="req">*</span></label>
            <select id="bf-service" name="bf_service" required>
              <option value="" disabled selected>Select a service</option>
              <option>AI Commercial Production</option>
              <option>CGI / FOOH Activation</option>
              <option>Premium Video Production</option>
              <option>Hybrid AI + CGI + VFX</option>
              <option>Not sure yet</option>
            </select>
          </div>
          <div class="field">
            <label for="bf-budget">Budget <span class="req">*</span></label>
            <select id="bf-budget" name="bf_budget" required>
              <option value="" disabled selected>Select budget</option>
              <option>$3,000 – $5,000</option>
              <option>$5,000 – $10,000</option>
              <option>$10,000 – $15,000</option>
              <option>$15,000+</option>
              <option>Not confirmed yet</option>
            </select>
          </div>
        </div>

        <div class="field">
          <label for="bf-project">Project Idea <span class="opt">— optional</span></label>
          <textarea id="bf-project" name="bf_project" placeholder="Describe the campaign, product, launch, reference, location, or visual idea..."></textarea>
        </div>

        <div class="submit-row">
          <button type="submit" class="brief-submit">Submit Brief</button>
        </div>

        <p class="form-note">For faster response, you can also send references or a short voice note through WhatsApp.</p>
      </form>
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
