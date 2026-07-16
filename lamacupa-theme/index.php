<?php
/**
 * Fallback generico (archivi / blog). Il sito Lamacupa usa pagine statiche.
 *
 * @package Lamacupa
 */
get_header(); ?>

<section class="block">
	<div class="wrap prose">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				echo '<h1 style="margin-bottom:24px">' . esc_html( get_the_title() ) . '</h1>';
				the_content();
			endwhile;
		else :
			echo '<p>Nessun contenuto.</p>';
		endif;
		?>
	</div>
</section>

<?php get_footer(); ?>
