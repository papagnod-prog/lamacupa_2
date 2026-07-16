<?php
/**
 * Pagina "Gli Orci" — corpo modificabile dall'editor WordPress.
 *
 * @package Lamacupa
 */
get_header();
if ( have_posts() ) :
	the_post();
	lamacupa_page_hero( 'Edizione speciale' );
	?>
	<section class="block" style="padding-top:50px">
		<div class="wrap"><div class="prose"><?php the_content(); ?></div></div>
	</section>

	<section class="block" style="background:var(--cream-2);text-align:center">
		<div class="wrap center">
			<span class="eyebrow" data-en="Request">Su richiesta</span>
			<h2 style="font-size:clamp(28px,3.6vw,44px);font-weight:400" data-en="Customised jars &amp; gifts">Orci e regali personalizzati</h2>
			<a class="btn btn-gold" href="<?php echo esc_url( home_url( '/contatti/' ) ); ?>" style="margin-top:30px" data-en="Request a quote">Richiedi un preventivo</a>
		</div>
	</section>
	<?php
endif;
get_footer();
