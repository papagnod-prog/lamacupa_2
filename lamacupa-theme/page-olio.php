<?php
/**
 * Pagina "L'Olio" — corpo modificabile dall'editor WordPress + CTA allo shop.
 *
 * @package Lamacupa
 */
get_header();
if ( have_posts() ) :
	the_post();
	lamacupa_page_hero( 'Olio Extra Vergine di Oliva' );
	?>
	<section class="block" style="padding-top:50px">
		<div class="wrap"><div class="prose"><?php the_content(); ?></div></div>
	</section>

	<section class="block" style="background:var(--paper);text-align:center">
		<div class="wrap center">
			<span class="eyebrow" data-en="Bring it home">Portalo a casa</span>
			<h2 style="font-size:clamp(28px,3.6vw,44px);font-weight:400" data-en="Choose your format of Luma">Scegli il tuo formato di Luma</h2>
			<p class="lead" data-en="From the 100 ml to taste it, to the 500 ml for everyday use.">Dal 100 ml per assaggiarlo, al 500 ml per l'uso quotidiano.</p>
			<a class="btn btn-gold" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" style="margin-top:30px" data-en="Go to shop">Vai allo shop</a>
		</div>
	</section>
	<?php
endif;
get_footer();
