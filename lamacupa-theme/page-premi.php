<?php
/**
 * Pagina "Premi" — corpo modificabile dall'editor WordPress.
 *
 * @package Lamacupa
 */
get_header();
if ( have_posts() ) :
	the_post();
	lamacupa_page_hero( 'Premi &amp; Riconoscimenti' );
	?>
	<section class="block" style="padding-top:50px">
		<div class="wrap"><div class="prose"><?php the_content(); ?></div></div>
	</section>
	<?php
endif;
get_footer();
