<?php
/**
 * Template Name: Homepage
 * Description: DeepStudio particle animation front page
 *
 * 100% content from original index.html — only image path changed to WordPress URI.
 *
 * @package DeepStudio
 */

get_header();
?>

<div id="canvas-container">
	<canvas id="particleCanvas"></canvas>
</div>

<div id="ui-layer">
	<div class="logo-container" id="logo-anchor">
		<canvas id="logo-canvas"></canvas>
	</div>
	<canvas id="text-canvas"></canvas>
</div>

<?php get_footer(); ?>
