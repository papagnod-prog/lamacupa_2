<?php
/**
 * Articolo singolo del blog "Appunti d'olio".
 *
 * @package Lamacupa
 */
get_header();
if ( have_posts() ) :
	the_post();
	$cats     = get_the_category();
	$catname  = $cats ? $cats[0]->name : 'Appunti';
	$hero_img = has_post_thumbnail() ? get_the_post_thumbnail_url( null, 'full' ) : '';
	$hero_st  = $hero_img ? ' style="background-image:url(\'' . esc_url( $hero_img ) . '\')"' : '';
	?>
	<section class="pagehero post-hero">
		<div class="imgslot"<?php echo $hero_st; // phpcs:ignore ?>></div>
		<div class="wrap inner">
			<span class="eyebrow"><?php echo esc_html( $catname ); ?></span>
			<h1><?php the_title(); ?></h1>
			<div class="post-meta"><?php echo esc_html( get_the_date() ); ?> &middot; <?php echo esc_html( get_the_author() ); ?></div>
		</div>
	</section>
	<div class="wrap"><div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> &middot; <a href="<?php echo esc_url( home_url( '/appunti/' ) ); ?>" data-en="Journal">Appunti</a> &middot; <span><?php the_title(); ?></span></div></div>

	<section class="block" style="padding-top:46px">
		<div class="wrap"><div class="prose">
			<?php the_content(); ?>
		</div></div>
	</section>

	<?php
	/* Articoli correlati: stessa categoria, esclusi questo. */
	$cat_ids = wp_list_pluck( $cats, 'term_id' );
	$related = new WP_Query( array(
		'post_type'           => 'post',
		'posts_per_page'      => 3,
		'post__not_in'        => array( get_the_ID() ),
		'ignore_sticky_posts' => true,
		'category__in'        => $cat_ids ? $cat_ids : array(),
		'orderby'             => 'date',
		'order'               => 'DESC',
	) );
	if ( $related->have_posts() ) : ?>
		<section class="block" style="background:var(--paper)">
			<div class="wrap">
				<div class="shop-head"><div><span class="eyebrow" data-en="Keep reading">Continua a leggere</span><h2 style="font-size:clamp(26px,3vw,38px);font-weight:400" data-en="Related notes">Appunti correlati</h2></div>
					<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/appunti/' ) ); ?>" data-en="All articles">Tutti gli articoli</a></div>
				<div class="notes">
					<?php while ( $related->have_posts() ) : $related->the_post(); lamacupa_blog_card( get_the_ID() ); endwhile; ?>
				</div>
			</div>
		</section>
		<?php wp_reset_postdata(); ?>
	<?php endif; ?>

	<section class="block news">
		<div class="wrap center">
			<span class="eyebrow" data-en="Newsletter">Newsletter</span>
			<h2 data-en="Oil notes, straight to your inbox">Appunti d'olio, dritti nella tua casella</h2>
			<form onsubmit="return false">
				<input type="email" placeholder="La tua email" data-en-ph="Your email" />
				<button class="btn btn-gold" type="submit" data-en="Subscribe">Iscriviti</button>
			</form>
		</div>
	</section>
	<?php
endif;
get_footer();
