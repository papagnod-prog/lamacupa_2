<?php
/**
 * Plugin Name:       Lamacupa — Pannello Contenuti
 * Plugin URI:        https://lamacupa.it
 * Description:        Pannello di gestione contenuti per il sito Lamacupa: testi, immagini, colori, font, banner/slide e prodotti. Include lo shortcode [lamacupa_home] e applica le impostazioni dal vivo al front-end.
 * Version:           1.8.0
 * Requires at least: 5.6
 * Requires PHP:      7.0
 * Author:            Lamacupa
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       lamacupa-pannello
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LMCP_VER', '1.8.0' );
define( 'LMCP_DIR', plugin_dir_path( __FILE__ ) );
define( 'LMCP_URL', plugin_dir_url( __FILE__ ) );
define( 'LMCP_OPT', 'lamacupa_pannello_config' );

/* ============================================================
 * DEFAULT + SANITIZZAZIONE  (inline: nessuna dipendenza da file
 * esterni in fase di attivazione, per evitare errori fatali)
 * ========================================================== */

if ( ! function_exists( 'lmcp_defaults' ) ) {
	/**
	 * Valori di default — identici allo stato originale del sito Lamacupa.
	 */
	function lmcp_defaults() {
		$d = array(
			'appearance' => array(
				'cream' => '#f8f3e7',
				'olive' => '#34401f',
				'gold'  => '#b88a3e',
				'font'  => 'Jost',
			),
			'banner'     => array(
				'enabled'  => true,
				'rotate'   => 4,
				'messages' => array(
					'Spedizione gratuita in Italia per ordini superiori a <b>&euro;59</b> &middot; Filiera corta &middot; Km Zero',
				),
			),
			'hero'       => array(
				'eyebrow'  => 'Monocultivar Coratina &middot; 100% Italiano',
				'title'    => "L'oro verde<br>di <em>Corato</em>",
				'subtitle' => 'Un olio extra vergine di carattere, estratto a freddo dagli ulivi secolari della nostra terra. Dalla pianta alla tavola, senza intermediari.',
				'cta1'     => 'Acquista ora',
				'cta2'     => 'La nostra storia',
				'rotate'   => 6,
				'slides'   => array(
					array(
						'eyebrow'  => 'Monocultivar Coratina &middot; 100% Italiano',
						'title'    => "L'oro verde<br>di <em>Corato</em>",
						'subtitle' => 'Un olio extra vergine di carattere, estratto a freddo dagli ulivi secolari della nostra terra. Dalla pianta alla tavola, senza intermediari.',
						'cta1'     => 'Acquista ora',
						'cta2'     => 'La nostra storia',
						'image'    => '',
					),
					array(
						'eyebrow'  => 'Edizione speciale',
						'title'    => "Gli Orci,<br>un <em>filo d'oro</em> di ricordi",
						'subtitle' => "L'olio custodito come un tempo: orci in terracotta lavorati a mano, un dono che profuma di tradizione.",
						'cta1'     => 'Scopri gli Orci',
						'cta2'     => 'Idee regalo',
						'image'    => '',
					),
					array(
						'eyebrow'  => 'Raccolto 2025 &middot; Appena franto',
						'title'    => 'Dalla raccolta al frantoio<br>in <em>24 ore</em>',
						'subtitle' => 'Spedizione gratuita in Italia oltre &euro;59. Il fruttato intenso della Coratina arriva direttamente a casa tua.',
						'cta1'     => 'Vai allo shop',
						'cta2'     => 'Come lavoriamo',
						'image'    => '',
					),
				),
			),
			'images'     => array(),
			'brand'      => array(
				'logoHeader' => '',
				'logoFooter' => '',
				'logoH'      => 34,
				'logoF'      => 40,
			),
			'pages'      => array(
				'storia'   => array( 'eyebrow' => 'La nostra storia', 'title' => "Una famiglia, una terra,<br>l'<em>oro verde</em> di Puglia", 'intro' => 'La storia che vogliamo raccontarvi passa attraverso i nostri prodotti. Vogliamo rendervi parte della nostra famiglia, tra la bellezza, le salde radici e il nettare dorato degli ulivi secolari della terra che amiamo.' ),
				'olio'     => array( 'eyebrow' => 'Olio Extra Vergine di Oliva', 'title' => '', 'intro' => '' ),
				'orci'     => array( 'eyebrow' => 'Edizione speciale', 'title' => "Gli Orci,<br>un <em>filo d'oro</em> di ricordi", 'intro' => "La tradizione che incontra il dono. I nostri orci in terracotta custodiscono l'olio Luma come si faceva un tempo, quando l'olio era un tesoro da conservare, da donare, da tramandare." ),
				'shop'     => array( 'eyebrow' => 'Lo Shop', 'title' => 'Tutti i nostri prodotti', 'intro' => '' ),
				'premi'    => array( 'eyebrow' => 'Premi &amp; Riconoscimenti', 'title' => 'Una qualit&agrave;<br><em>riconosciuta</em>', 'intro' => "La nostra grande passione per l'olio &egrave; stata riconosciuta diverse volte. &Egrave; solo grazie a un lavoro attento, in campo e in frantoio, che si ottiene un olio da premio." ),
				'appunti'  => array( 'eyebrow' => "Appunti d'olio", 'title' => 'Prendiamo appunti da generazioni', 'intro' => '' ),
				'contatti' => array( 'eyebrow' => 'Contatti', 'title' => 'Parliamone', 'intro' => '' ),
			),
			'shop'       => array(
				array( 'name' => 'Luma 250ml', 'cat' => 'Coratina monovarietale', 'price' => '&euro;12,00', 'tag' => 'Best seller' ),
				array( 'name' => 'Luma 500ml', 'cat' => 'Coratina monovarietale', 'price' => '&euro;19,00', 'tag' => '' ),
				array( 'name' => 'Orcio in terracotta', 'cat' => 'Edizione speciale 500ml', 'price' => '&euro;34,00', 'tag' => 'Idea regalo' ),
				array( 'name' => 'Confezione Degustazione', 'cat' => '3 x 100ml', 'price' => '&euro;22,00', 'tag' => '' ),
			),
		);

		// Stile per-testo di default (vuoto = eredita dal tema) per ogni pagina.
		foreach ( $d['pages'] as $slug => $pg ) {
			$d['pages'][ $slug ]['style'] = array(
				'eyebrow' => array( 'color' => '', 'font' => '' ),
				'title'   => array( 'color' => '', 'font' => '' ),
				'intro'   => array( 'color' => '', 'font' => '' ),
			);
		}

		return $d;
	}
}

if ( ! function_exists( 'lmcp_sanitize_config' ) ) {
	/**
	 * Sanitizzazione leggera della config in arrivo dal pannello.
	 */
	function lmcp_sanitize_config( $in ) {
		$allowed = array(
			'b'      => array(),
			'em'     => array(),
			'br'     => array(),
			'strong' => array(),
			'i'      => array(),
		);
		$out = lmcp_defaults();

		if ( isset( $in['appearance'] ) && is_array( $in['appearance'] ) ) {
			foreach ( array( 'cream', 'olive', 'gold' ) as $k ) {
				if ( ! empty( $in['appearance'][ $k ] ) ) {
					$hex = sanitize_hex_color( $in['appearance'][ $k ] );
					if ( $hex ) {
						$out['appearance'][ $k ] = $hex;
					}
				}
			}
			if ( ! empty( $in['appearance']['font'] ) ) {
				$out['appearance']['font'] = sanitize_text_field( $in['appearance']['font'] );
			}
		}

		if ( isset( $in['banner'] ) && is_array( $in['banner'] ) ) {
			$out['banner']['enabled']  = ! empty( $in['banner']['enabled'] );
			$out['banner']['rotate']   = isset( $in['banner']['rotate'] ) ? max( 2, (int) $in['banner']['rotate'] ) : 4;
			$out['banner']['messages'] = array();
			if ( ! empty( $in['banner']['messages'] ) && is_array( $in['banner']['messages'] ) ) {
				foreach ( $in['banner']['messages'] as $m ) {
					$out['banner']['messages'][] = wp_kses( (string) $m, $allowed );
				}
			}
		}

		if ( isset( $in['hero'] ) && is_array( $in['hero'] ) ) {
			foreach ( array( 'eyebrow', 'title', 'subtitle', 'cta1', 'cta2' ) as $k ) {
				if ( isset( $in['hero'][ $k ] ) ) {
					$out['hero'][ $k ] = wp_kses( (string) $in['hero'][ $k ], $allowed );
				}
			}
			$out['hero']['rotate'] = isset( $in['hero']['rotate'] ) ? max( 3, min( 15, (int) $in['hero']['rotate'] ) ) : 6;
			if ( isset( $in['hero']['slides'] ) && is_array( $in['hero']['slides'] ) ) {
				$out['hero']['slides'] = array();
				foreach ( $in['hero']['slides'] as $s ) {
					if ( ! is_array( $s ) ) {
						continue;
					}
					$out['hero']['slides'][] = array(
						'eyebrow'  => isset( $s['eyebrow'] ) ? wp_kses( (string) $s['eyebrow'], $allowed ) : '',
						'title'    => isset( $s['title'] ) ? wp_kses( (string) $s['title'], $allowed ) : '',
						'subtitle' => isset( $s['subtitle'] ) ? wp_kses( (string) $s['subtitle'], $allowed ) : '',
						'cta1'     => isset( $s['cta1'] ) ? wp_kses( (string) $s['cta1'], $allowed ) : '',
						'cta2'     => isset( $s['cta2'] ) ? wp_kses( (string) $s['cta2'], $allowed ) : '',
						'image'    => isset( $s['image'] ) ? esc_url_raw( (string) $s['image'] ) : '',
					);
				}
				if ( empty( $out['hero']['slides'] ) ) {
					$def = lmcp_defaults();
					$out['hero']['slides'] = $def['hero']['slides'];
				}
			}
		}

		if ( isset( $in['images'] ) && is_array( $in['images'] ) ) {
			$out['images'] = array();
			foreach ( $in['images'] as $id => $url ) {
				$id  = sanitize_key( $id );
				$url = esc_url_raw( (string) $url );
				if ( $id && $url ) {
					$out['images'][ $id ] = $url;
				}
			}
		}

		if ( isset( $in['shop'] ) && is_array( $in['shop'] ) ) {
			$out['shop'] = array();
			foreach ( $in['shop'] as $p ) {
				if ( ! is_array( $p ) ) {
					continue;
				}
				$out['shop'][] = array(
					'name'  => isset( $p['name'] ) ? wp_kses( (string) $p['name'], $allowed ) : '',
					'cat'   => isset( $p['cat'] ) ? wp_kses( (string) $p['cat'], $allowed ) : '',
					'price' => isset( $p['price'] ) ? sanitize_text_field( (string) $p['price'] ) : '',
					'tag'   => isset( $p['tag'] ) ? sanitize_text_field( (string) $p['tag'] ) : '',
				);
			}
		}

		if ( isset( $in['brand'] ) && is_array( $in['brand'] ) ) {
			$out['brand']['logoHeader'] = isset( $in['brand']['logoHeader'] ) ? esc_url_raw( (string) $in['brand']['logoHeader'] ) : '';
			$out['brand']['logoFooter'] = isset( $in['brand']['logoFooter'] ) ? esc_url_raw( (string) $in['brand']['logoFooter'] ) : '';
			$out['brand']['logoH']      = isset( $in['brand']['logoH'] ) ? max( 16, min( 120, (int) $in['brand']['logoH'] ) ) : 34;
			$out['brand']['logoF']      = isset( $in['brand']['logoF'] ) ? max( 16, min( 120, (int) $in['brand']['logoF'] ) ) : 40;
		}

		if ( isset( $in['pages'] ) && is_array( $in['pages'] ) ) {
			$fonts = array( '', 'Jost', 'Manrope', 'Cormorant Garamond', 'EB Garamond', 'Spectral' );
			$out['pages'] = array();
			foreach ( $in['pages'] as $slug => $pg ) {
				if ( ! is_array( $pg ) ) {
					continue;
				}
				$slug = sanitize_key( $slug );
				$style = array();
				foreach ( array( 'eyebrow', 'title', 'intro' ) as $part ) {
					$c = isset( $pg['style'][ $part ]['color'] ) ? sanitize_hex_color( $pg['style'][ $part ]['color'] ) : '';
					$f = isset( $pg['style'][ $part ]['font'] ) ? (string) $pg['style'][ $part ]['font'] : '';
					$style[ $part ] = array(
						'color' => $c ? $c : '',
						'font'  => in_array( $f, $fonts, true ) ? $f : '',
					);
				}
				$out['pages'][ $slug ] = array(
					'eyebrow' => isset( $pg['eyebrow'] ) ? wp_kses( (string) $pg['eyebrow'], $allowed ) : '',
					'title'   => isset( $pg['title'] ) ? wp_kses( (string) $pg['title'], $allowed ) : '',
					'intro'   => isset( $pg['intro'] ) ? wp_kses( (string) $pg['intro'], $allowed ) : '',
					'style'   => $style,
				);
			}
		}

		return $out;
	}
}

if ( ! function_exists( 'lmcp_get_config' ) ) {
	function lmcp_get_config() {
		$cfg = get_option( LMCP_OPT, null );
		if ( ! is_array( $cfg ) ) {
			$cfg = lmcp_defaults();
		}
		return $cfg;
	}
}

if ( ! function_exists( 'lmcp_admin_data' ) ) {
	function lmcp_admin_data() {
		return array(
			'ajax'     => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'lmcp_save' ),
			'config'   => lmcp_get_config(),
			'defaults' => lmcp_defaults(),
			'preview'  => home_url( '/' ),
		);
	}
}

if ( ! function_exists( 'lmcp_img_style' ) ) {
	/**
	 * Stile inline background-image per un'area immagine (usato dal template).
	 */
	function lmcp_img_style( $imgs, $id ) {
		if ( ! empty( $imgs[ $id ] ) ) {
			return ' style="background-image:url(\'' . esc_url( $imgs[ $id ] ) . '\')"';
		}
		return '';
	}
}

/* ============================================================
 * AREA ADMIN
 * ========================================================== */

add_action( 'admin_menu', 'lmcp_register_menu' );
function lmcp_register_menu() {
	add_menu_page(
		'Lamacupa',
		'Lamacupa',
		'manage_options',
		'lamacupa-pannello',
		'lmcp_render_admin_page',
		'dashicons-palmtree',
		58
	);
}

/* ---- Asset del pannello (incluso direttamente in wp-admin, senza iframe) ---- */
add_action( 'admin_enqueue_scripts', 'lmcp_admin_assets' );
function lmcp_admin_assets( $hook ) {
	if ( 'toplevel_page_lamacupa-pannello' !== $hook ) {
		return;
	}
	// Libreria Media di WordPress per la scelta delle immagini dal pannello.
	wp_enqueue_media();
	wp_enqueue_style(
		'lmcp-admin-fonts',
		'https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Manrope:wght@400;500;600;700&family=Cormorant+Garamond:wght@400;500;600&family=EB+Garamond:wght@400;500;600&family=Spectral:wght@300;400;500;600&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'lmcp-admin', LMCP_URL . 'admin/admin.css', array(), LMCP_VER );
	wp_enqueue_script( 'lmcp-admin', LMCP_URL . 'admin/admin.js', array(), LMCP_VER, true );
	wp_localize_script( 'lmcp-admin', 'LMCP_ADMIN', lmcp_admin_data() );
}

/**
 * (Legacy) Serve la UI come documento autonomo dentro un iframe.
 * Mantenuto per compatibilità con eventuali link diretti; il pannello
 * viene ora incluso direttamente nella pagina admin.
 */
add_action( 'admin_init', 'lmcp_maybe_render_canvas' );
function lmcp_maybe_render_canvas() {
	if ( ! isset( $_GET['page'], $_GET['lmcp_canvas'] ) || 'lamacupa-pannello' !== $_GET['page'] ) {
		return;
	}
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Accesso negato.' );
	}
	$lmcp_admin_data = lmcp_admin_data();
	$file = LMCP_DIR . 'admin/panel-canvas.php';
	header( 'Content-Type: text/html; charset=utf-8' );
	if ( file_exists( $file ) ) {
		include $file;
	} else {
		echo 'File del pannello mancante: admin/panel-canvas.php';
	}
	exit;
}

function lmcp_render_admin_page() {
	echo '<style>#wpbody-content{padding-bottom:0!important}#wpfooter{display:none}</style>';
	$file = LMCP_DIR . 'admin/panel-inline.php';
	if ( file_exists( $file ) ) {
		include $file;
	} else {
		echo '<div class="notice notice-error"><p>File del pannello mancante: admin/panel-inline.php</p></div>';
	}
}

/* ---- AJAX ---- */
add_action( 'wp_ajax_lmcp_save', 'lmcp_ajax_save' );
function lmcp_ajax_save() {
	check_ajax_referer( 'lmcp_save', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	$raw = isset( $_POST['config'] ) ? wp_unslash( $_POST['config'] ) : '';
	$cfg = json_decode( $raw, true );
	if ( ! is_array( $cfg ) ) {
		wp_send_json_error( 'bad_json', 400 );
	}
	update_option( LMCP_OPT, lmcp_sanitize_config( $cfg ) );
	wp_send_json_success( true );
}

add_action( 'wp_ajax_lmcp_reset', 'lmcp_ajax_reset' );
function lmcp_ajax_reset() {
	check_ajax_referer( 'lmcp_save', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'forbidden', 403 );
	}
	delete_option( LMCP_OPT );
	wp_send_json_success( lmcp_defaults() );
}

/* ============================================================
 * FRONT-END
 * ========================================================== */

add_action( 'wp_enqueue_scripts', 'lmcp_enqueue_front' );
function lmcp_enqueue_front() {
	wp_enqueue_style(
		'lmcp-fonts',
		'https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;1,400&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'lmcp-site', LMCP_URL . 'public/site.css', array(), LMCP_VER );
	wp_enqueue_script( 'lmcp-runtime', LMCP_URL . 'public/cms.js', array(), LMCP_VER, true );
	wp_localize_script( 'lmcp-runtime', 'LamacupaCMSData', lmcp_get_config() );
}

/* ---- Shortcode ---- */
add_shortcode( 'lamacupa_home', 'lmcp_shortcode_home' );
function lmcp_shortcode_home() {
	$file = LMCP_DIR . 'templates/home.php';
	if ( ! file_exists( $file ) ) {
		return '';
	}
	ob_start();
	include $file;
	return ob_get_clean();
}
