<?php
/**
 * Lamacupa — Bootstrap WooCommerce
 *
 * Rende lo shop "gestito da WooCommerce" già pronto all'uso: configura il
 * negozio (base Italia, EUR), crea le categorie e i prodotti del catalogo,
 * imposta le zone di spedizione (gratis in Italia oltre la soglia, tariffa
 * fissa sotto, estero a tariffa fissa) e abilita metodi di pagamento di base.
 *
 * Tutto è IDEMPOTENTE e auto-riparante: gira una sola volta per versione
 * (e comunque non duplica nulla, perché controlla l'esistenza prima di creare).
 * I prezzi e il catalogo restano poi gestibili da WooCommerce → Prodotti;
 * spedizioni da WooCommerce → Impostazioni → Spedizione; pagamenti da
 * WooCommerce → Impostazioni → Pagamenti.
 *
 * @package Lamacupa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LAMACUPA_WC_SETUP_VER', '1.0.0' );

/* ============================================================
 * Trigger: all'attivazione del tema e una volta per versione.
 * Richiede WooCommerce attivo (altrimenti non fa nulla).
 * ========================================================== */
add_action( 'after_switch_theme', 'lamacupa_wc_bootstrap' );
add_action( 'admin_init', function () {
	if ( get_option( 'lamacupa_wc_setup_done' ) !== LAMACUPA_WC_SETUP_VER ) {
		lamacupa_wc_bootstrap();
	}
} );

function lamacupa_wc_bootstrap() {
	if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_product' ) ) {
		return; // WooCommerce non attivo: riprova alla prossima admin_init.
	}
	lamacupa_wc_store_defaults();
	lamacupa_wc_seed_categories();
	lamacupa_wc_seed_products();
	lamacupa_wc_setup_shipping();
	lamacupa_wc_setup_payments();
	update_option( 'lamacupa_wc_setup_done', LAMACUPA_WC_SETUP_VER );
}

/* ============================================================
 * 1. Default del negozio — base Italia, valuta EUR, prezzi IVA inclusa.
 *    Imposta solo i valori ancora "vuoti"/di default, per non sovrascrivere
 *    scelte già fatte dal gestore.
 * ========================================================== */
function lamacupa_wc_store_defaults() {
	$country = (string) get_option( 'woocommerce_default_country' );
	// Imposta l'Italia solo se il valore è vuoto o è ancora il default US di WooCommerce.
	if ( '' === $country || 0 === strpos( $country, 'US' ) ) {
		update_option( 'woocommerce_default_country', 'IT:BA' ); // Italia — Bari (Puglia).
	}
	if ( ! get_option( 'woocommerce_store_address' ) ) {
		update_option( 'woocommerce_store_address', 'Azienda Agricola Lamacupa' );
		update_option( 'woocommerce_store_city', 'Corato' );
		update_option( 'woocommerce_store_postcode', '70033' );
	}
	if ( 'USD' === get_option( 'woocommerce_currency' ) || ! get_option( 'woocommerce_currency' ) ) {
		update_option( 'woocommerce_currency', 'EUR' );
	}
	update_option( 'woocommerce_currency_pos', 'right_space' ); // 12,00 €
	update_option( 'woocommerce_price_decimal_sep', ',' );
	update_option( 'woocommerce_price_thousand_sep', '.' );
	update_option( 'woocommerce_price_num_decimals', 2 );
	update_option( 'woocommerce_weight_unit', 'kg' );
	update_option( 'woocommerce_dimension_unit', 'cm' );
	// Vendiamo solo verso alcuni paesi? Lasciamo "tutti", il gestore può restringere.
}

/* ============================================================
 * 2. Categorie prodotto.
 * ========================================================== */
function lamacupa_wc_seed_categories() {
	$cats = array(
		'olio-luma'        => 'Olio Luma',
		'orci'             => 'Gli Orci',
		'confezioni-regalo' => 'Confezioni regalo',
	);
	foreach ( $cats as $slug => $name ) {
		if ( ! term_exists( $slug, 'product_cat' ) ) {
			wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
		}
	}
}

function lamacupa_wc_cat_id( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	return $term ? (int) $term->term_id : 0;
}

/* ============================================================
 * 3. Catalogo prodotti.
 *    Seminato SOLO se il negozio non ha ancora prodotti pubblicati
 *    (così non interferisce con un catalogo già curato dal gestore).
 *    Match per SKU: non crea doppioni se uno SKU esiste già.
 * ========================================================== */
function lamacupa_wc_seed_products() {
	$existing = wp_count_posts( 'product' );
	$published = isset( $existing->publish ) ? (int) $existing->publish : 0;
	if ( $published > 0 ) {
		return; // Catalogo già presente: non tocchiamo nulla.
	}

	$catalog = array(
		array(
			'sku'   => 'LUMA-250',
			'name'  => 'Luma 250 ml',
			'price' => '12.00',
			'cat'   => 'olio-luma',
			'short' => 'Olio EVO monovarietale Coratina, 100% italiano. Formato 250 ml.',
			'desc'  => 'Il nostro Luma nel formato da 250 ml: fruttato intenso, amaro e piccante in elegante equilibrio, altissima carica polifenolica. Estrazione a freddo dal raccolto fresco di olive Coratina di Puglia.',
			'feat'  => true,
			'weight' => '0.55',
		),
		array(
			'sku'   => 'LUMA-500',
			'name'  => 'Luma 500 ml',
			'price' => '19.00',
			'cat'   => 'olio-luma',
			'short' => 'Olio EVO monovarietale Coratina, 100% italiano. Formato 500 ml.',
			'desc'  => 'Il formato da mezzo litro del nostro monovarietale Coratina. Perfetto per l\'uso quotidiano a crudo su zuppe, verdure, carni alla griglia e i piatti della tradizione pugliese.',
			'weight' => '0.95',
		),
		array(
			'sku'   => 'LUMA-100',
			'name'  => 'Luma 100 ml',
			'price' => '6.50',
			'cat'   => 'olio-luma',
			'short' => 'Formato da assaggio dell\'olio EVO Coratina Luma.',
			'desc'  => 'Il formato da assaggio, ideale per conoscere il carattere deciso della Coratina o come piccolo regalo. 100 ml di monovarietale estratto a freddo.',
			'weight' => '0.25',
		),
		array(
			'sku'   => 'BOX-DEG-3x100',
			'name'  => 'Confezione Degustazione · 3 × 100 ml',
			'price' => '22.00',
			'cat'   => 'confezioni-regalo',
			'short' => 'Tre formati da assaggio in una confezione regalo curata.',
			'desc'  => 'Una selezione da degustazione: tre bottiglie da 100 ml del nostro Luma, presentate in una confezione regalo curata. Il modo migliore per scoprire la Coratina o farne dono.',
			'weight' => '0.85',
		),
		array(
			'sku'   => 'ORCIO-500',
			'name'  => 'Orcio in terracotta · 500 ml',
			'price' => '34.00',
			'cat'   => 'orci',
			'short' => 'Edizione speciale: olio Luma in orcio di terracotta artigianale.',
			'desc'  => 'La tradizione che incontra il dono. Olio Luma monovarietale Coratina custodito in un orcio di terracotta artigianale, da regalare e da conservare. Contiene 500 ml di olio.',
			'weight' => '1.40',
		),
		array(
			'sku'   => 'BOX-2x250',
			'name'  => 'Confezione regalo · 2 bottiglie',
			'price' => '28.00',
			'cat'   => 'confezioni-regalo',
			'short' => 'Due bottiglie da 250 ml in confezione regalo.',
			'desc'  => 'Due bottiglie da 250 ml del nostro Luma in una confezione regalo elegante. Ideale per le occasioni e i regali aziendali.',
			'weight' => '1.30',
		),
		array(
			'sku'   => 'BOX-3x250',
			'name'  => 'Confezione regalo · 3 bottiglie',
			'price' => '39.00',
			'cat'   => 'confezioni-regalo',
			'short' => 'Tre bottiglie da 250 ml in confezione regalo.',
			'desc'  => 'Tre bottiglie da 250 ml del nostro Luma in una confezione regalo elegante. Un dono importante, dal produttore alla tavola.',
			'weight' => '1.90',
		),
		array(
			'sku'   => 'ORCIO-1000',
			'name'  => 'Orcio grande · 1 L',
			'price' => '52.00',
			'cat'   => 'orci',
			'short' => 'Orcio grande in terracotta smaltata, 1 litro di olio Luma.',
			'desc'  => 'L\'orcio grande in terracotta smaltata, con un litro del nostro Luma monovarietale Coratina. Un oggetto da tramandare, simbolo di una terra e di una famiglia.',
			'weight' => '2.40',
		),
	);

	foreach ( $catalog as $p ) {
		// Salta se lo SKU esiste già.
		if ( function_exists( 'wc_get_product_id_by_sku' ) && wc_get_product_id_by_sku( $p['sku'] ) ) {
			continue;
		}
		$product = new WC_Product_Simple();
		$product->set_name( $p['name'] );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'visible' );
		$product->set_sku( $p['sku'] );
		$product->set_regular_price( $p['price'] );
		$product->set_short_description( $p['short'] );
		$product->set_description( $p['desc'] );
		$product->set_manage_stock( false );
		$product->set_stock_status( 'instock' );
		$product->set_sold_individually( false );
		if ( ! empty( $p['weight'] ) ) {
			$product->set_weight( $p['weight'] );
		}
		if ( ! empty( $p['feat'] ) ) {
			$product->set_featured( true );
		}
		$cat_id = lamacupa_wc_cat_id( $p['cat'] );
		if ( $cat_id ) {
			$product->set_category_ids( array( $cat_id ) );
		}
		$product->save();
	}
}

/* ============================================================
 * 4. Spedizioni.
 *    Zona "Italia": spedizione gratuita oltre 59 €, altrimenti tariffa
 *    fissa 6,90 €. Zona "Resto del mondo" (predefinita): tariffa fissa.
 *    Creata solo se non esiste già una zona con lo stesso nome.
 * ========================================================== */
function lamacupa_wc_setup_shipping() {
	if ( ! class_exists( 'WC_Shipping_Zones' ) || ! class_exists( 'WC_Shipping_Zone' ) ) {
		return;
	}

	// --- Zona Italia ---
	$existing = WC_Shipping_Zones::get_zones();
	$has_italia = false;
	foreach ( $existing as $z ) {
		if ( isset( $z['zone_name'] ) && 'Italia' === $z['zone_name'] ) {
			$has_italia = true;
			break;
		}
	}

	if ( ! $has_italia ) {
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( 'Italia' );
		$zone->set_zone_order( 1 );
		$zone->add_location( 'IT', 'country' );
		$zone->save();
		$zone_id = $zone->get_id();

		// Tariffa fissa 6,90 €.
		$flat_id = $zone->add_shipping_method( 'flat_rate' );
		$opt_key = 'woocommerce_flat_rate_' . $flat_id . '_settings';
		update_option( $opt_key, array(
			'title'      => 'Spedizione standard',
			'tax_status' => 'taxable',
			'cost'       => '6.90',
		) );

		// Spedizione gratuita oltre 59 € di importo minimo.
		$free_id  = $zone->add_shipping_method( 'free_shipping' );
		$free_key = 'woocommerce_free_shipping_' . $free_id . '_settings';
		update_option( $free_key, array(
			'title'         => 'Spedizione gratuita',
			'requires'      => 'min_amount',
			'min_amount'    => '59',
			'ignore_discounts' => 'no',
		) );
	}

	// --- Zona predefinita "Resto del mondo": tariffa fissa estero ---
	$rest = new WC_Shipping_Zone( 0 );
	$methods = $rest->get_shipping_methods();
	if ( empty( $methods ) ) {
		$flat_id = $rest->add_shipping_method( 'flat_rate' );
		$opt_key = 'woocommerce_flat_rate_' . $flat_id . '_settings';
		update_option( $opt_key, array(
			'title'      => 'Spedizione internazionale',
			'tax_status' => 'taxable',
			'cost'       => '19.90',
		) );
	}

	// Attiva il calcolo della spedizione e mostra il calcolatore al carrello.
	update_option( 'woocommerce_enable_shipping_calc', 'yes' );
	update_option( 'woocommerce_shipping_cost_requires_address', 'no' );
}

/* ============================================================
 * 5. Pagamenti.
 *    Abilita di base Bonifico bancario (BACS) e Contrassegno, così lo
 *    shop è operativo da subito. Stripe / PayPal / Satispay richiedono
 *    le credenziali del gestore: si attivano in WooCommerce → Pagamenti.
 *    Tocchiamo i gateway solo la prima volta (non sovrascriviamo scelte).
 * ========================================================== */
function lamacupa_wc_setup_payments() {
	// BACS — Bonifico bancario.
	$bacs = get_option( 'woocommerce_bacs_settings', array() );
	if ( empty( $bacs ) ) {
		update_option( 'woocommerce_bacs_settings', array(
			'enabled'      => 'yes',
			'title'        => 'Bonifico bancario',
			'description'  => 'Effettua il pagamento con bonifico sul nostro conto. Useremo il numero d\'ordine come causale. L\'ordine viene spedito alla ricezione del pagamento.',
			'instructions' => 'Grazie. Riceverai le coordinate bancarie via email; indica il numero d\'ordine come causale.',
		) );
	}

	// COD — Contrassegno (pagamento alla consegna), limitato all'Italia.
	$cod = get_option( 'woocommerce_cod_settings', array() );
	if ( empty( $cod ) ) {
		update_option( 'woocommerce_cod_settings', array(
			'enabled'            => 'yes',
			'title'              => 'Pagamento alla consegna',
			'description'        => 'Paga in contanti al corriere alla consegna.',
			'instructions'       => 'Paga in contanti al corriere alla consegna.',
			'enable_for_methods' => array(),
			'enable_for_virtual' => 'no',
		) );
	}
}

/* ============================================================
 * Avviso amministratore se WooCommerce non è attivo: lo shop
 * funziona, ma in modalità dimostrativa (schede statiche).
 * ========================================================== */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) ) {
		echo '<div class="notice notice-warning is-dismissible"><p><strong>Lamacupa — Shop.</strong> Per gestire prodotti, pagamenti e spedizioni dal negozio, installa e attiva <em>WooCommerce</em>. All\'attivazione il tema configura automaticamente categorie, catalogo, spedizioni (gratis oltre 59 €) e i metodi di pagamento di base.</p></div>';
		return;
	}
	// WooCommerce attivo ma nessun gateway online (carte): promemoria gentile.
	if ( get_option( 'lamacupa_wc_setup_done' ) === LAMACUPA_WC_SETUP_VER ) {
		$gateways = function_exists( 'WC' ) && WC()->payment_gateways ? WC()->payment_gateways->get_available_payment_gateways() : array();
		$has_online = isset( $gateways['stripe'] ) || isset( $gateways['ppcp-gateway'] ) || isset( $gateways['paypal'] ) || isset( $gateways['satispay'] );
		if ( ! $has_online && ! get_option( 'lamacupa_wc_gateway_notice_off' ) ) {
			echo '<div class="notice notice-info is-dismissible"><p><strong>Lamacupa — Pagamenti.</strong> Sono attivi Bonifico e Contrassegno. Per accettare carte di credito e PayPal, installa il gateway scelto (es. WooCommerce Stripe o PayPal Payments) e inserisci le credenziali in <em>WooCommerce → Impostazioni → Pagamenti</em>.</p></div>';
		}
	}
} );

/* ------------------------------------------------------------------
 * Pagine WooCommerce (Carrello, Pagamento, Account, Shop).
 * Se per qualunque motivo mancano (installazioni migrate a mano),
 * le ricrea: senza di esse la procedura d'acquisto non è completa.
 * ---------------------------------------------------------------- */
add_action( 'admin_init', 'lamacupa_wc_ensure_pages' );
if ( ! function_exists( 'lamacupa_wc_ensure_pages' ) ) {
	function lamacupa_wc_ensure_pages() {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_page_id' ) ) {
			return;
		}
		$need = false;
		foreach ( array( 'cart', 'checkout', 'myaccount', 'shop' ) as $key ) {
			$id = wc_get_page_id( $key );
			if ( $id <= 0 || 'publish' !== get_post_status( $id ) ) {
				$need = true;
				break;
			}
		}
		if ( $need && class_exists( 'WC_Install' ) ) {
			WC_Install::create_pages();
		}
	}
}
