<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfg  = lmcp_get_config();
$ap   = isset( $cfg['appearance'] ) ? $cfg['appearance'] : array();
$cream = ! empty( $ap['cream'] ) ? $ap['cream'] : '#f8f3e7';
$olive = ! empty( $ap['olive'] ) ? $ap['olive'] : '#34401f';
$gold  = ! empty( $ap['gold'] )  ? $ap['gold']  : '#b88a3e';
$fontName = ! empty( $ap['font'] ) ? $ap['font'] : 'Jost';

$font_stacks = array(
	'Jost'               => "'Jost',system-ui,sans-serif",
	'Manrope'            => "'Manrope',system-ui,sans-serif",
	'Cormorant Garamond' => "'Cormorant Garamond',Georgia,serif",
	'EB Garamond'        => "'EB Garamond',Georgia,serif",
	'Spectral'           => "'Spectral',Georgia,serif",
);
$font_stack = isset( $font_stacks[ $fontName ] ) ? $font_stacks[ $fontName ] : $font_stacks['Jost'];

$banner   = isset( $cfg['banner'] ) ? $cfg['banner'] : array();
$messages = ! empty( $banner['messages'] ) ? $banner['messages'] : array();
$first_msg = $messages ? $messages[0] : '';
$banner_on = ! isset( $banner['enabled'] ) || $banner['enabled'];

$hero = isset( $cfg['hero'] ) ? $cfg['hero'] : array();
$shop = isset( $cfg['shop'] ) && is_array( $cfg['shop'] ) ? $cfg['shop'] : array();
$imgs = isset( $cfg['images'] ) && is_array( $cfg['images'] ) ? $cfg['images'] : array();

$kses = array( 'b' => array(), 'em' => array(), 'br' => array(), 'strong' => array(), 'i' => array() );
?>
<style id="lmcp-home-appearance">
.lmcp-home{
  --cream:<?php echo esc_html( $cream ); ?>;
  --cream-2:color-mix(in srgb, <?php echo esc_html( $cream ); ?> 88%, #2b2a20);
  --paper:color-mix(in srgb, <?php echo esc_html( $cream ); ?> 78%, #ffffff);
  --olive:<?php echo esc_html( $olive ); ?>;
  --olive-deep:color-mix(in srgb, <?php echo esc_html( $olive ); ?> 74%, #000000);
  --olive-soft:color-mix(in srgb, <?php echo esc_html( $olive ); ?> 60%, #c8cf9c);
  --gold:<?php echo esc_html( $gold ); ?>;
  --gold-deep:color-mix(in srgb, <?php echo esc_html( $gold ); ?> 78%, #000000);
  --line:color-mix(in srgb, <?php echo esc_html( $olive ); ?> 16%, transparent);
  --maxw:1280px;
  font-family:<?php echo $font_stack; // phpcs:ignore ?>;
  color:var(--ink,#2b2a20);background:var(--cream);
}
.lmcp-home .imgslot{position:absolute;inset:0;width:100%;height:100%;background-size:cover;background-position:center;background-color:var(--cream-2)}
.lmcp-home .hero .imgslot{z-index:0}
.lmcp-home .hero-nav{display:flex;gap:26px;list-style:none;margin:0;padding:0}
.lmcp-home .hero-nav a{font-size:14px;color:var(--ink,#2b2a20)}
.lmcp-home img{display:block;max-width:100%}
</style>

<div class="lmcp-home">

	<?php if ( $banner_on ) : ?>
	<div class="announce"><?php echo wp_kses( $first_msg, $kses ); ?></div>
	<?php endif; ?>

	<header>
		<div class="wrap">
			<div class="nav">
				<div class="brand">
					<span class="mark">LAMACUPA</span>
					<span class="tag">Olio EVO · Corato</span>
				</div>
				<ul class="hero-nav">
					<li><a href="#prodotti">Prodotti</a></li>
					<li><a href="#prodotti">Shop</a></li>
				</ul>
			</div>
		</div>
	</header>

	<!-- HERO -->
	<section class="hero">
		<div class="imgslot" id="a-hero"<?php echo lmcp_img_style( $imgs, 'a-hero' ); ?>></div>
		<div class="wrap hero-inner">
			<span class="eyebrow"><?php echo wp_kses( isset( $hero['eyebrow'] ) ? $hero['eyebrow'] : '', $kses ); ?></span>
			<h1><?php echo wp_kses( isset( $hero['title'] ) ? $hero['title'] : '', $kses ); ?></h1>
			<p><?php echo wp_kses( isset( $hero['subtitle'] ) ? $hero['subtitle'] : '', $kses ); ?></p>
			<div class="hero-cta">
				<a class="btn btn-gold" href="#prodotti"><?php echo wp_kses( isset( $hero['cta1'] ) ? $hero['cta1'] : '', $kses ); ?></a>
				<a class="btn btn-ghost" href="#prodotti"><?php echo wp_kses( isset( $hero['cta2'] ) ? $hero['cta2'] : '', $kses ); ?></a>
			</div>
		</div>
	</section>

	<!-- SELEZIONE -->
	<section class="block" id="prodotti">
		<div class="wrap">
			<div class="shop-head">
				<div>
					<span class="eyebrow">La selezione</span>
					<h2>I nostri prodotti</h2>
				</div>
			</div>
			<div class="cards">
				<?php
				$slots = array( 'a-p1', 'a-p2', 'a-p3', 'a-p4' );
				foreach ( $shop as $i => $p ) :
					$slot = isset( $slots[ $i ] ) ? $slots[ $i ] : 'a-p' . ( $i + 1 );
					$tag  = isset( $p['tag'] ) ? $p['tag'] : '';
					?>
					<div class="card">
						<div class="card-media">
							<?php if ( '' !== trim( (string) $tag ) ) : ?>
								<span class="card-tag"><?php echo esc_html( $tag ); ?></span>
							<?php endif; ?>
							<div class="imgslot" id="<?php echo esc_attr( $slot ); ?>"<?php echo lmcp_img_style( $imgs, $slot ); ?>></div>
						</div>
						<div class="card-body">
							<h4><?php echo wp_kses( isset( $p['name'] ) ? $p['name'] : '', $kses ); ?></h4>
							<div class="ct"><?php echo wp_kses( isset( $p['cat'] ) ? $p['cat'] : '', $kses ); ?></div>
							<div class="card-foot">
								<span class="p"><?php echo esc_html( isset( $p['price'] ) ? $p['price'] : '' ); ?></span>
								<span class="add">+ Carrello</span>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<footer>
		<div class="wrap">
			<div class="foot-bottom">
				<span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Azienda Agricola Lamacupa · Corato (BA)</span>
				<span>Olio Extra Vergine di Oliva · Monocultivar Coratina</span>
			</div>
		</div>
	</footer>

</div>
