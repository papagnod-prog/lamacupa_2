<?php
/**
 * Pagina generica (per Pagine senza template dedicato page-{slug}.php).
 *
 * @package Lamacupa
 */
get_header(); ?>

<?php if ( ! is_front_page() ) : ?>
<section class="pagehero">
	<div class="imgslot" id="page-hero"></div>
	<div class="wrap inner">
		<span class="eyebrow"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
		<h1><?php the_title(); ?></h1>
	</div>
</section>
<?php endif; ?>

<section class="block">
	<div class="wrap prose">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				the_content();
			endwhile;
		endif;
		?>
	</div>
</section>

<?php get_footer(); ?>
