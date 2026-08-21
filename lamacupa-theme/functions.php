<?php
/**
 * Tema Lamacupa — funzioni.
 *
 * Pensato per funzionare insieme al plugin "Lamacupa — Pannello Contenuti"
 * (lamacupa-pannello), che inietta il runtime e la configurazione gestita
 * dalla dashboard. Il tema fornisce il markup (classi .announce, .hero,
 * .cards, .imgslot con i relativi id) su cui il pannello agisce.
 *
 * @package Lamacupa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LAMACUPA_THEME_VER', '1.5.0' );

/* ---- Moduli del tema ---- */
// Bootstrap WooCommerce: configura negozio, categorie, prodotti, spedizioni e pagamenti.
require_once get_template_directory() . '/inc/woocommerce-setup.php';
// Compatibilità con le shortcode Woodmart/WPBakery negli articoli importati dal vecchio sito.
require_once get_template_directory() . '/inc/legacy-shortcodes.php';

/* ---- Supporti del tema ---- */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => 'Menu principale' ) );

	/* WooCommerce: il tema fornisce il proprio markup carrello/shop. */
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
} );

/* Il tema è full-width senza sidebar: rimuove la sidebar di default che
 * WooCommerce mostra su Shop e pagina prodotto (mostrava i widget
 * standard "Pagine/Archivi/Categorie" mai configurati). */
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar' );

/* Le pagine WooCommerce di default (prodotto singolo, carrello, checkout,
 * "il mio account"…) usano di serie un wrapper <main> senza contenitore:
 * il contenuto tocca i bordi della pagina invece di stare dentro .wrap
 * come nel resto del sito. Sostituiamo il wrapper con quello del tema. */
remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
add_action( 'woocommerce_before_main_content', function () {
	echo '<div class="wrap" style="padding:50px 40px">';
} );
add_action( 'woocommerce_after_main_content', function () {
	echo '</div>';
} );

/* ---- Menu di fallback (se nessun menu è assegnato in Aspetto → Menu) ---- */
function lamacupa_default_menu() {
	$items = array(
		''         => 'Home',
		'storia'   => 'La Storia',
		'olio'     => "L'Olio",
		'orci'     => 'Gli Orci',
		'shop'     => 'Shop',
		'premi'    => 'Premi',
		'appunti'  => 'Appunti',
		'contatti' => 'Contatti',
	);
	echo '<ul class="menu">';
	foreach ( $items as $slug => $label ) {
		$url    = $slug ? home_url( '/' . $slug . '/' ) : home_url( '/' );
		$active = ( '' === $slug && is_front_page() ) || ( $slug && is_page( $slug ) ) ? ' class="active"' : '';
		echo '<li><a' . $active . ' href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>'; // phpcs:ignore
	}
	echo '</ul>';
}

/* ---- Asset front-end ---- */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'lamacupa-fonts',
		'https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);
	// Il design system completo è dentro style.css del tema.
	wp_enqueue_style( 'lamacupa-style', get_stylesheet_uri(), array(), LAMACUPA_THEME_VER );
	// Comportamenti: lingua IT/EN, menu mobile, carrello, formati.
	wp_enqueue_script( 'lamacupa-site', get_template_directory_uri() . '/assets/site.js', array(), LAMACUPA_THEME_VER, true );

	// Drawer carrello laterale (richiede WooCommerce per l'aggiunta reale al carrello).
	wp_enqueue_script( 'lamacupa-cart', get_template_directory_uri() . '/assets/cart.js', array(), LAMACUPA_THEME_VER, true );
	wp_localize_script( 'lamacupa-cart', 'LamacupaCart', array(
		'hasWoo'      => function_exists( 'WC' ),
		'cartUrl'     => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/shop/' ),
		'checkoutUrl' => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/shop/' ),
		'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
	) );

	// Lightbox per le gallerie foto negli articoli del blog.
	if ( is_singular( 'post' ) ) {
		wp_enqueue_script( 'lamacupa-lightbox', get_template_directory_uri() . '/assets/lightbox.js', array(), LAMACUPA_THEME_VER, true );
	}
} );

/* ---- Drawer carrello: markup stampato a fine pagina su tutto il sito ---- */
add_action( 'wp_footer', 'lamacupa_cart_drawer' );
function lamacupa_cart_drawer() {
	$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/shop/' );
	$checkout_url = function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/shop/' );
	?>
	<div class="cart-overlay" data-cart-overlay></div>
	<aside class="cart-drawer" data-cart-drawer aria-hidden="true" aria-label="Carrello">
		<div class="cart-head">
			<span class="cart-title" data-en="Your cart">Il tuo carrello</span>
			<button class="cart-close" type="button" data-cart-close aria-label="Chiudi"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
		</div>
		<div class="cart-body">
			<div class="widget_shopping_cart_content">
				<?php if ( function_exists( 'woocommerce_mini_cart' ) ) { woocommerce_mini_cart(); } ?>
			</div>
		</div>
		<div class="cart-foot">
			<p class="cart-ship" data-en="Shipping calculated at checkout, based on your address.">Spedizione calcolata al checkout, in base al tuo indirizzo.</p>
			<a class="btn btn-ghost" href="<?php echo esc_url( $cart_url ); ?>" data-en="Go to cart">Vai al carrello</a>
			<a class="btn btn-gold" href="<?php echo esc_url( $checkout_url ); ?>" data-en="Checkout">Procedi al pagamento</a>
		</div>
	</aside>
	<?php
}

/* ---- Aggiorna il contatore carrello nell'header via fragments AJAX ---- */
add_filter( 'woocommerce_add_to_cart_fragments', 'lamacupa_cart_fragments' );
function lamacupa_cart_fragments( $fragments ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $fragments;
	}
	$count = WC()->cart->get_cart_contents_count();
	$fragments['span.cart-count'] = '<span class="cart-count">' . esc_html( $count ) . '</span>';
	return $fragments;
}

/* ---- Creazione automatica di pagine + permalink (idempotente, auto-riparante) ---- */
add_action( 'after_switch_theme', 'lamacupa_setup_pages' );
add_action( 'admin_init', function () {
	if ( get_option( 'lamacupa_setup_done' ) !== LAMACUPA_THEME_VER ) {
		lamacupa_setup_pages();
	}
} );
function lamacupa_setup_pages() {
	$pages = array(
		'home'     => 'Home',
		'storia'   => 'La Storia',
		'olio'     => "L'Olio",
		'orci'     => 'Gli Orci',
		'shop'     => 'Shop',
		'premi'    => 'Premi',
		'appunti'  => 'Appunti',
		'contatti' => 'Contatti',
	);
	$home_id = 0;
	foreach ( $pages as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			if ( 'home' === $slug ) {
				$home_id = $existing->ID;
			}
			continue;
		}
		$id = wp_insert_post( array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '',
		) );
		if ( 'home' === $slug && $id && ! is_wp_error( $id ) ) {
			$home_id = $id;
		}
	}
	// Home come front page statica.
	if ( $home_id ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}
	// I template page-{slug}.php richiedono permalink "carini" (non "Semplice").
	$struct = get_option( 'permalink_structure' );
	if ( empty( $struct ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
	}
	flush_rewrite_rules( false );
	update_option( 'lamacupa_setup_done', LAMACUPA_THEME_VER );
}

/* ---- Slug della pagina corrente (per l'editing per-pagina del pannello) ---- */
function lamacupa_current_slug() {
	if ( is_front_page() ) {
		return 'home';
	}
	if ( is_page() ) {
		$obj = get_queried_object();
		if ( $obj && isset( $obj->post_name ) ) {
			return $obj->post_name;
		}
	}
	return '';
}

/* ---- Avviso se il plugin del pannello non è attivo ---- */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! function_exists( 'lmcp_get_config' ) ) {
		echo '<div class="notice notice-info is-dismissible"><p><strong>Tema Lamacupa.</strong> Per gestire testi, colori, banner e prodotti dalla dashboard, installa e attiva anche il plugin <em>“Lamacupa — Pannello Contenuti”</em> (lamacupa-pannello).</p></div>';
	}
} );

/* ============================================================
 * INTESTAZIONE DI PAGINA (hero) condivisa
 * Titolo = titolo della pagina (modificabile dall'editor WordPress).
 * Immagine = immagine in evidenza (Media) con fallback al pannello.
 * ========================================================== */
function lamacupa_page_hero( $eyebrow_fallback = '' ) {
	$slug = function_exists( 'lamacupa_current_slug' ) ? lamacupa_current_slug() : '';
	$cfg  = function_exists( 'lmcp_get_config' ) ? lmcp_get_config() : array();
	$pg   = ( $slug && isset( $cfg['pages'][ $slug ] ) ) ? $cfg['pages'][ $slug ] : array();
	$eyebrow = ! empty( $pg['eyebrow'] ) ? $pg['eyebrow'] : $eyebrow_fallback;

	$img = '';
	if ( has_post_thumbnail() ) {
		$img = get_the_post_thumbnail_url( null, 'full' );
	} elseif ( $slug && ! empty( $cfg['images'][ $slug . '-hero' ] ) ) {
		$img = $cfg['images'][ $slug . '-hero' ];
	}
	$style = $img ? ' style="background-image:url(\'' . esc_url( $img ) . '\')"' : '';
	?>
	<section class="pagehero">
		<div class="imgslot" id="<?php echo esc_attr( $slug ); ?>-hero"<?php echo $style; // phpcs:ignore ?>></div>
		<div class="wrap inner">
			<?php if ( $eyebrow ) : ?><span class="eyebrow"><?php echo wp_kses( $eyebrow, array( 'em' => array(), 'br' => array() ) ); ?></span><?php endif; ?>
			<h1><?php echo esc_html( get_the_title() ); ?></h1>
		</div>
	</section>
	<div class="wrap"><div class="crumb"><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> &middot; <span><?php echo esc_html( get_the_title() ); ?></span></div></div>
	<?php
}

/* ============================================================
 * BLOG — scheda articolo (riusa le classi .note)
 * ========================================================== */
function lamacupa_blog_card( $post_id = null ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$cats    = get_the_category( $post_id );
	$catname = ! empty( $cats ) ? $cats[0]->name : 'Appunti';
	$thumb   = has_post_thumbnail( $post_id )
		? '<img src="' . esc_url( get_the_post_thumbnail_url( $post_id, 'large' ) ) . '" alt="" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover">'
		: '<div class="imgslot"></div>';
	?>
	<a class="note" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
		<div class="note-media"><?php echo $thumb; // phpcs:ignore ?></div>
		<div class="date"><?php echo esc_html( $catname ); ?></div>
		<h4><?php echo esc_html( get_the_title( $post_id ) ); ?></h4>
		<p><?php echo esc_html( wp_trim_words( get_the_excerpt( $post_id ), 22 ) ); ?></p>
	</a>
	<?php
}

/* ============================================================
 * SEED dei contenuti (testi modificabili) e degli articoli demo.
 * Idempotente: riempie solo ciò che è vuoto, una sola volta per versione.
 * ========================================================== */
add_action( 'after_switch_theme', 'lamacupa_seed_all' );
add_action( 'admin_init', function () {
	if ( get_option( 'lamacupa_content_seeded' ) !== LAMACUPA_THEME_VER ) {
		lamacupa_seed_all();
	}
} );
function lamacupa_seed_all() {
	lamacupa_seed_pages_content();
	lamacupa_seed_posts();
	update_option( 'lamacupa_content_seeded', LAMACUPA_THEME_VER );
}

function lamacupa_block_p( $text, $intro = false ) {
	if ( $intro ) {
		return "<!-- wp:paragraph {\"className\":\"intro\"} -->\n<p class=\"intro\">" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
	}
	return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
}
function lamacupa_block_h( $text ) {
	return "<!-- wp:heading -->\n<h2>" . $text . "</h2>\n<!-- /wp:heading -->\n\n";
}

function lamacupa_default_content() {
	$c = array();

	$c['storia'] =
		lamacupa_block_p( "La storia che vogliamo raccontarvi passa attraverso i nostri prodotti. Vogliamo rendervi parte della nostra famiglia, tra la bellezza, le salde radici e il nettare dorato degli ulivi secolari della terra che amiamo.", true ) .
		lamacupa_block_h( "L'Azienda Agricola «Lamacupa»" ) .
		lamacupa_block_p( "Situata nel cuore della Puglia, l'azienda agricola «Lamacupa» vanta una storia semplice e naturale, fatta di passione e sacrifici. Una famiglia, una terra, sapori e odori di un prodotto eccellente che abbiamo deciso di donare nelle vostre mani, sulle vostre tavole." ) .
		lamacupa_block_p( "L'azienda, a conduzione famigliare, nasce da un progetto di vita vissuta: siamo Maria Rosa Arbore e Luigi Malcangi, compagni di vita e appassionati agricoltori da generazioni, alla costante ricerca della perfezione e della qualità." ) .
		lamacupa_block_h( "Dalla pianta alla tavola" ) .
		lamacupa_block_p( "Nella nostra azienda si respirano attenzione, passione e dedizione: dalla cura della pianta alla raccolta dei frutti, fino alla delicata fase di trasformazione in olio EVO, all'imbottigliamento e alla cura nella conservazione di un prodotto di qualità eccellente." ) .
		lamacupa_block_p( "Produciamo olio dalla monocultivar «Coratina», varietà tipica della Puglia, con una carica polifenolica doppia rispetto alle altre varietà. L'estrazione a freddo consente di preservare tutte le qualità presenti naturalmente in questa cultivar." );

	$c['olio'] =
		lamacupa_block_p( "Dal caratteristico profumo intenso della Coratina 100%, un olio EVO di altissima qualità dallo spiccato sentore fruttato e dall'altissimo contenuto di polifenoli.", true ) .
		lamacupa_block_p( "Il suo carattere deciso incanta con un amaro elegante. Un olio dal fruttato intenso, di grande complessità, dal profumo ampio che richiama oliva verde, erba fresca, carciofo e mandorla. Gusto equilibrato, con note piccanti e amare caratteristiche della varietà, dalla forte identità mediterranea." ) .
		lamacupa_block_h( "Scheda di degustazione" ) .
		lamacupa_block_p( "Varietà: Coratina · Raccolta: fine novembre / inizio dicembre · Colore: giallo-verde intenso · Olfatto: note verdi, di carciofo, mandorla e rucola · Gusto: ampio, con amaro e piccante in elegante equilibrio." ) .
		lamacupa_block_h( "In cucina" ) .
		lamacupa_block_p( "Ideale a crudo su zuppe di legumi, carni alla griglia, verdure e polpo bollito. Si esalta nella cucina della tradizione pugliese: orecchiette con le cime di rapa, crema di fave e cicoria, bruschette di pane casereccio." );

	$c['orci'] =
		lamacupa_block_p( "La tradizione che incontra il dono. I nostri orci in terracotta custodiscono l'olio Luma come si faceva un tempo, quando l'olio era un tesoro da conservare, da donare, da tramandare.", true ) .
		lamacupa_block_h( "Terracotta artigianale, l'oro verde all'interno" ) .
		lamacupa_block_p( "Ogni orcio è un oggetto da regalare e da conservare, simbolo di una terra e di una famiglia. All'interno, il nostro Luma monovarietale Coratina: la stessa cura, in un contenitore che racconta da dove viene." ) .
		lamacupa_block_h( "Per i tuoi eventi" ) .
		lamacupa_block_p( "Perfetti per matrimoni, eventi speciali e regali aziendali. Realizziamo forniture personalizzate con bigliettini e dediche: scrivici e costruiamo insieme la proposta giusta." );

	$c['premi'] =
		lamacupa_block_p( "La nostra grande passione per l'olio ci è stata riconosciuta diverse volte. È solo grazie a un lavoro attento, in campo e in frantoio, che si ottiene un olio da premio.", true ) .
		lamacupa_block_h( "I riconoscimenti" ) .
		lamacupa_block_p( "2026 — Fruttato Intenso, 1° classificato (categoria Coratina Premium, concorso nazionale).<br>2025 — Olio EVO dell'anno (guida regionale di settore).<br>2025 — Gran Menzione (sezione monocultivar fruttato intenso, concorso internazionale).<br>2024 — Eccellenza Coratina (selezione olio EVO di Puglia).<br>2024 — Menzione di merito (qualità e sostenibilità)." ) .
		lamacupa_block_h( "La qualità nasce nell'uliveto" ) .
		lamacupa_block_p( "Raccolta nel momento giusto, estrazione a freddo, conservazione attenta: ogni scelta protegge i polifenoli e i profumi della Coratina. È questo che un premio riconosce: l'integrità di un prodotto eccellente." );

	$c['contatti'] =
		lamacupa_block_p( "Per ordini, orci personalizzati, regali aziendali o semplicemente per un saluto: siamo qui.", true ) .
		lamacupa_block_h( "Dove siamo" ) .
		lamacupa_block_p( "Azienda Agricola Lamacupa<br>Corato (BA), Puglia — Italia" ) .
		lamacupa_block_h( "Contatti" ) .
		lamacupa_block_p( "Email: <a href=\"mailto:info@lamacupa.it\">info@lamacupa.it</a><br>Telefono: <a href=\"tel:+39000000000\">+39 000 000 0000</a><br>Social: <a href=\"#\">Instagram</a> · <a href=\"#\">Facebook</a>" );

	return $c;
}

function lamacupa_seed_pages_content() {
	$content = lamacupa_default_content();
	foreach ( $content as $slug => $html ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			continue;
		}
		if ( '' === trim( (string) $page->post_content ) ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => $html ) );
		}
	}
}

function lamacupa_seed_posts() {
	// Solo se il blog è sostanzialmente vuoto (al più il "Hello World" di default).
	$count = (int) wp_count_posts( 'post' )->publish;
	if ( $count > 1 ) {
		return;
	}
	$cat_id = 0;
	$term   = term_exists( 'appunti-dolio', 'category' );
	if ( ! $term ) {
		$term = wp_insert_term( "Appunti d'olio", 'category', array( 'slug' => 'appunti-dolio' ) );
	}
	if ( ! is_wp_error( $term ) ) {
		$cat_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
	}

	$posts = array(
		array(
			'title'   => 'I polifenoli: cosa sono, dove trovarli e perché fanno così bene',
			'excerpt' => 'La cultivar Coratina ha una carica polifenolica doppia rispetto alle altre varietà. Vi spieghiamo cosa sono e perché contano, per il gusto e per la salute.',
			'body'    => "I polifenoli sono antiossidanti naturali che proteggono l'olio e chi lo consuma. Nella Coratina sono particolarmente abbondanti.",
		),
		array(
			'title'   => 'Coratina a crudo: gli abbinamenti della tradizione pugliese',
			'excerpt' => 'Orecchiette con le cime di rapa, crema di fave e cicoria, bruschette di pane casereccio.',
			'body'    => "Un filo d'olio a crudo esalta i piatti più semplici della tradizione. Ecco i nostri abbinamenti preferiti.",
		),
		array(
			'title'   => 'Dalla pianta alla bottiglia: il nostro novembre',
			'excerpt' => 'Come scegliamo il momento della raccolta per proteggere profumi e polifenoli.',
			'body'    => "La raccolta è il momento più importante dell'anno: anticiparla di pochi giorni cambia il carattere dell'olio.",
		),
		array(
			'title'   => '5 test per riconoscere l\'olio extravergine migliore',
			'excerpt' => 'Colore, profumo, gusto: piccole prove casalinghe per riconoscere un grande olio.',
			'body'    => "Non serve essere assaggiatori professionisti: bastano pochi accorgimenti per distinguere un extravergine di qualità.",
		),
	);
	$i = 0;
	foreach ( $posts as $p ) {
		$id = wp_insert_post( array(
			'post_title'   => $p['title'],
			'post_excerpt' => $p['excerpt'],
			'post_content' => lamacupa_block_p( $p['excerpt'], true ) . lamacupa_block_p( $p['body'] ),
			'post_status'  => 'publish',
			'post_type'    => 'post',
			'post_date'    => gmdate( 'Y-m-d H:i:s', strtotime( '-' . ( $i * 9 ) . ' days' ) ),
		) );
		if ( $id && ! is_wp_error( $id ) && $cat_id ) {
			wp_set_post_categories( $id, array( $cat_id ) );
		}
		$i++;
	}
}
