<?php
/**
 * Header del tema Lamacupa.
 *
 * @package Lamacupa
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?> data-lmcp-page="<?php echo esc_attr( function_exists( 'lamacupa_current_slug' ) ? lamacupa_current_slug() : '' ); ?>">
<?php wp_body_open(); ?>
<div class="site">

	<div class="announce" data-en="Free shipping in Italy on orders over <b>&euro;59</b> &middot; Short supply chain &middot; Zero km">Spedizione gratuita in Italia per ordini superiori a <b>&euro;59</b> &middot; Filiera corta &middot; Km Zero</div>

	<header>
		<div class="wrap nav">
			<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="mark">LAMACUPA</span>
			</a>
			<nav>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'menu',
					'fallback_cb'    => 'lamacupa_default_menu',
					'depth'          => 2,
				) );
				?>
			</nav>
			<div class="nav-right">
				<button class="lang"><b>IT</b> / EN</button>
				<a class="icnbtn" href="<?php echo esc_url( home_url( '/contatti/' ) ); ?>" aria-label="Account"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></a>
				<?php $lamacupa_cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/shop/' ); ?>
				<button class="icnbtn" type="button" data-open-cart aria-label="Carrello"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 7h12l-1.2 12.5a1 1 0 0 1-1 .9H8.2a1 1 0 0 1-1-.9L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg><span class="cart-count"><?php echo function_exists( 'WC' ) && WC()->cart ? esc_html( WC()->cart->get_cart_contents_count() ) : '0'; ?></span></button>
				<button class="burger" aria-label="Menu"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 6h18M3 12h18M3 18h18"/></svg></button>
			</div>
		</div>
	</header>
