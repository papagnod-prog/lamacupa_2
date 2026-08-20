<?php
/**
 * Compatibilità con i contenuti degli articoli importati dal vecchio sito,
 * scritti con WPBakery Page Builder + tema Woodmart (non installati qui).
 *
 * Ignora tutti i parametri di stile/layout dell'editor originale (colori,
 * spaziature responsive, parallax, ID custom...) e renderizza solo il
 * contenuto semantico (titoli, testo, gallerie, video) con lo stile del
 * nuovo tema — niente "peso" di Woodmart, solo il contenuto vero.
 *
 * @package Lamacupa
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Le shortcode migrate a volte hanno virgolette "eleganti" (” “) al posto
 * di quelle dritte (") nei valori degli attributi — probabile effetto di
 * una texturizzazione del testo salvata nel contenuto durante l'export dal
 * vecchio sito. WordPress non riconosce come shortcode un tag con
 * virgolette non dritte, quindi le sistemiamo PRIMA che scatti il parser —
 * solo dentro le shortcode che ci interessano, senza toccare le virgolette
 * eleganti nel testo normale degli articoli (che restano intatte).
 */
add_filter( 'the_content', function ( $content ) {
	$tags = 'vc_row|vc_column|vc_column_text|woodmart_title|woodmart_gallery|vc_video';
	return preg_replace_callback(
		'/\[\/?(?:' . $tags . ')\b[^\]]*\]/u',
		function ( $m ) {
			return str_replace( array( '”', '“' ), '"', $m[0] );
		},
		$content
	);
}, 1 );

add_shortcode( 'vc_row', function ( $atts, $content = '' ) {
	return '<div class="legacy-row">' . do_shortcode( (string) $content ) . '</div>';
} );

add_shortcode( 'vc_column', function ( $atts, $content = '' ) {
	return '<div class="legacy-col">' . do_shortcode( (string) $content ) . '</div>';
} );

add_shortcode( 'vc_column_text', function ( $atts, $content = '' ) {
	$content = wpautop( trim( (string) $content ) );
	// Righe che iniziano con "* " (elenco puntato "a mano" nel testo
	// originale) diventano un paragrafo con lo stile citazione/elenco.
	$content = preg_replace( '/<p>\s*\*\s*/', '<p class="legacy-bullet">', $content );
	return '<div class="legacy-text">' . $content . '</div>';
} );

add_shortcode( 'woodmart_title', function ( $atts ) {
	$atts = shortcode_atts( array(
		'title'       => '',
		'after_title' => '',
	), $atts, 'woodmart_title' );

	$html = '<div class="legacy-title">';
	if ( $atts['title'] ) {
		$html .= '<h2>' . esc_html( $atts['title'] ) . '</h2>';
	}
	if ( $atts['after_title'] ) {
		$lines = array_filter( array_map( 'trim', explode( "\n", $atts['after_title'] ) ) );
		if ( $lines ) {
			$html .= '<p class="legacy-subtitle">' . esc_html( implode( ' · ', $lines ) ) . '</p>';
		}
	}
	$html .= '</div>';
	return $html;
} );

add_shortcode( 'woodmart_gallery', function ( $atts ) {
	$atts = shortcode_atts( array( 'images' => '' ), $atts, 'woodmart_gallery' );
	$ids  = array_filter( array_map( 'trim', explode( ',', $atts['images'] ) ) );
	if ( ! $ids ) {
		return '';
	}

	$html = '<div class="legacy-gallery">';
	foreach ( $ids as $id ) {
		if ( wp_attachment_is_image( $id ) ) {
			$html .= wp_get_attachment_image( $id, 'medium_large', false, array( 'loading' => 'lazy' ) );
		}
	}
	$html .= '</div>';
	return $html;
} );

add_shortcode( 'vc_video', function ( $atts ) {
	$atts = shortcode_atts( array( 'link' => '' ), $atts, 'vc_video' );
	if ( ! $atts['link'] ) {
		return '';
	}
	$embed = wp_oembed_get( esc_url( $atts['link'] ) );
	return $embed ? '<div class="legacy-video">' . $embed . '</div>' : '';
} );
