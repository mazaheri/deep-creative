<?php
/**
 * Main template fallback (blog index / 404 context)
 *
 * @package DeepStudio
 */

get_header();
?>

<main id="primary" style="color:#fff;padding:2rem;font-family:Inter,sans-serif;">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No content found.', 'deepstudio' ); ?></p>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
