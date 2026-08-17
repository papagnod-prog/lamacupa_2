<?php
/**
 * Plugin Name: Lamacupa Preordina
 * Description: Attiva il preordine con sconto su singoli prodotti — acquisto diretto (no raccolta email), integrato con carrello e checkout WooCommerce. Permette l'acquisto anche se il prodotto risulta "Esaurito" (senza modificare l'etichetta di disponibilità mostrata in pagina); marca l'articolo come preordine in carrello, email d'ordine e in un elenco ordini dedicato.
 * Version:     2.0.0
 * Author:      Simply APP / Origine Digitale
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LMPO_PREFIX', '_lmpo_' );

// ─────────────────────────────────────────────────────────────────────────────
// REQUISITO — WooCommerce attivo
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'plugins_loaded', function () {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', function () {
            echo '<div class="notice notice-error"><p><strong>Lamacupa Preordina</strong> richiede WooCommerce attivo.</p></div>';
        } );
    }
} );

// ─────────────────────────────────────────────────────────────────────────────
// ACQUISTO ANCHE DA "ESAURITO" — solo per prodotti con preordine attivo.
//
// Il badge/etichetta "Esaurito" mostrato in pagina prodotto/shop NON cambia:
// i filtri sotto restituiscono "disponibile" solo quando ENTRAMBE le
// condizioni sono vere — (1) siamo in un contesto di carrello/checkout
// (aggiunta al carrello, pagina carrello, pagina checkout, o le relative
// azioni AJAX) e (2) il prodotto specifico ha il preordine attivo. Sulla
// pagina prodotto o nello shop (nessuna di queste condizioni) il valore
// originale passa invariato, quindi "Esaurito" resta visibile come prima.
// ─────────────────────────────────────────────────────────────────────────────

function lmpo_stock_bypass_active(): bool {
    static $active = null;
    if ( $active !== null ) {
        return $active;
    }
    $active = false;

    // Aggiunta al carrello — form classico o AJAX (product_id + quantity)
    if ( isset( $_REQUEST['add-to-cart'] ) || ( isset( $_REQUEST['product_id'] ) && isset( $_REQUEST['quantity'] ) ) ) {
        $active = true;
    }
    // Pagina carrello o checkout (la revalidazione scorte avviene qui ad ogni caricamento)
    if ( ! $active && function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
        $active = true;
    }
    // Azioni AJAX WooCommerce di carrello/checkout (aggiorna carrello, applica coupon, ricalcola ordine…)
    if ( ! $active && isset( $_REQUEST['wc-ajax'] ) ) {
        $ajax_actions = array( 'add_to_cart', 'update_cart', 'update_order_review', 'checkout', 'apply_coupon', 'remove_coupon', 'get_refreshed_fragments' );
        if ( in_array( wp_unslash( $_REQUEST['wc-ajax'] ), $ajax_actions, true ) ) {
            $active = true;
        }
    }
    return $active;
}

// Nota: le variazioni usano lo STESSO filtro dei prodotti semplici per
// is_in_stock() — non esiste un filtro separato "per variazione" in
// WooCommerce — quindi un solo filtro copre entrambi i casi, purché
// controlli anche l'id del prodotto "padre" tramite lmpo_is_enabled_for().
add_filter( 'woocommerce_product_is_in_stock', function ( $in_stock, $product ) {
    if ( $in_stock || ! $product ) return $in_stock;
    if ( lmpo_stock_bypass_active() && lmpo_is_enabled_for( $product ) ) {
        return true;
    }
    return $in_stock;
}, 999, 2 );

add_filter( 'woocommerce_product_backorders_allowed', function ( $allowed, $product_id, $product ) {
    if ( $allowed ) return $allowed;
    if ( lmpo_stock_bypass_active() && lmpo_is_enabled_for( $product ) ) {
        return true;
    }
    return $allowed;
}, 999, 3 );

// Selettore variazioni (prodotti variabili): il JS blocca il pulsante e mostra
// "Scegli un'altra combinazione" se la variazione risulta esaurita nei dati
// che WooCommerce invia al browser. Forziamo "disponibile" in quei dati per
// le variazioni con preordine attivo, senza toccare il loro stato reale.
add_filter( 'woocommerce_available_variation', function ( array $data, $product, $variation ) {
    if ( lmpo_is_enabled( $variation->get_id() ) || lmpo_is_enabled( $product->get_id() ) ) {
        $data['is_in_stock']    = true;
        $data['is_purchasable'] = true;
    }
    return $data;
}, 20, 3 );

// ─────────────────────────────────────────────────────────────────────────────
// CARRELLO/CHECKOUT — flag ordine + meta riga per email e amministrazione
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'woocommerce_checkout_create_order_line_item', function ( $item, $cart_item_key, $values, $order ) {
    $product_id = $values['product_id'] ?? 0;
    if ( ! lmpo_is_enabled( $product_id ) ) {
        return;
    }
    $date = get_post_meta( $product_id, LMPO_PREFIX . 'date', true );
    $item->add_meta_data( 'Preordine', 'Sì', true );
    if ( $date ) {
        $item->add_meta_data( 'Consegna prevista', $date, true );
    }
    $order->update_meta_data( '_lmpo_has_preorder', 'yes' );
}, 10, 4 );

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — elenco ordini in preordine, separato dagli altri
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_submenu_page(
        'woocommerce',
        'Ordini in preordine',
        '🕒 Preordini',
        'manage_woocommerce',
        'lmpo-preorders',
        'lmpo_render_preorders_page'
    );
} );

function lmpo_render_preorders_page(): void {
    $orders = wc_get_orders( array(
        'limit'      => 200,
        'orderby'    => 'date',
        'order'      => 'DESC',
        'meta_key'   => '_lmpo_has_preorder',
        'meta_value' => 'yes',
    ) );

    echo '<div class="wrap"><h1>🕒 Ordini in preordine</h1>';

    if ( ! $orders ) {
        echo '<p>Nessun ordine in preordine al momento.</p></div>';
        return;
    }

    echo '<table class="widefat striped" style="margin-top:16px">';
    echo '<thead><tr><th>Ordine</th><th>Data</th><th>Cliente</th><th>Stato</th><th>Totale</th><th>Prodotti in preordine</th></tr></thead><tbody>';

    foreach ( $orders as $order ) {
        $edit_link = method_exists( $order, 'get_edit_order_url' )
            ? $order->get_edit_order_url()
            : admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' );

        $names = array();
        foreach ( $order->get_items() as $item ) {
            if ( $item->get_meta( 'Preordine' ) ) {
                $delivery = $item->get_meta( 'Consegna prevista' );
                $names[]  = $item->get_name() . ( $delivery ? ' (' . $delivery . ')' : '' );
            }
        }

        printf(
            '<tr><td><a href="%s">#%s</a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
            esc_url( $edit_link ),
            esc_html( $order->get_order_number() ),
            esc_html( wc_format_datetime( $order->get_date_created() ) ),
            esc_html( $order->get_formatted_billing_full_name() ),
            esc_html( wc_get_order_status_name( $order->get_status() ) ),
            wp_kses_post( $order->get_formatted_order_total() ),
            esc_html( implode( ', ', $names ) )
        );
    }

    echo '</tbody></table></div>';
}

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — META BOX SUL PRODOTTO
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'lmpo_preorder_box',
        '🕒 Preordina (Lamacupa)',
        'lmpo_render_meta_box',
        'product',
        'side',
        'high'
    );
} );

function lmpo_render_meta_box( WP_Post $post ): void {
    wp_nonce_field( 'lmpo_save', 'lmpo_nonce' );

    $enabled  = get_post_meta( $post->ID, LMPO_PREFIX . 'enabled', true );
    $type     = get_post_meta( $post->ID, LMPO_PREFIX . 'discount_type', true ) ?: 'percentage';
    $value    = get_post_meta( $post->ID, LMPO_PREFIX . 'discount_value', true );
    $note     = get_post_meta( $post->ID, LMPO_PREFIX . 'note', true );
    $date     = get_post_meta( $post->ID, LMPO_PREFIX . 'date', true );
    $badge    = get_post_meta( $post->ID, LMPO_PREFIX . 'badge_text', true ) ?: 'Disponibile in preordine';
    ?>
    <p>
        <label style="font-weight:600;display:flex;align-items:center;gap:6px;">
            <input type="checkbox" name="lmpo_enabled" value="1" <?php checked( $enabled, '1' ); ?> style="width:18px;height:18px;">
            Attiva preordine per questo prodotto
        </label>
    </p>

    <div id="lmpo-fields" style="<?php echo $enabled === '1' ? '' : 'opacity:.4;pointer-events:none;'; ?>">

        <p>
            <label style="font-size:12px;font-weight:600;color:#555;">Testo badge</label><br>
            <input type="text" name="lmpo_badge_text" value="<?php echo esc_attr( $badge ); ?>" style="width:100%;">
        </p>

        <p>
            <label style="font-size:12px;font-weight:600;color:#555;">Tipo sconto</label><br>
            <select name="lmpo_discount_type" style="width:100%;">
                <option value="percentage" <?php selected( $type, 'percentage' ); ?>>Percentuale (%)</option>
                <option value="fixed" <?php selected( $type, 'fixed' ); ?>>Importo fisso (€)</option>
            </select>
        </p>

        <p>
            <label style="font-size:12px;font-weight:600;color:#555;">Valore sconto</label><br>
            <input type="number" step="0.01" min="0" name="lmpo_discount_value" value="<?php echo esc_attr( $value ); ?>" style="width:100%;" placeholder="es. 10">
        </p>

        <p>
            <label style="font-size:12px;font-weight:600;color:#555;">Nota (facoltativa)</label><br>
            <textarea name="lmpo_note" rows="2" style="width:100%;" placeholder="es. Prenota ora la nuova raccolta"><?php echo esc_textarea( $note ); ?></textarea>
        </p>

        <p>
            <label style="font-size:12px;font-weight:600;color:#555;">Disponibilità prevista (facoltativo)</label><br>
            <input type="text" name="lmpo_date" value="<?php echo esc_attr( $date ); ?>" style="width:100%;" placeholder="es. Novembre 2026">
        </p>

    </div>

    <p style="font-size:11px;color:#888;margin-top:10px;">
        Il plugin non forza la disponibilità: mostra badge, sconto e testo "Preordina" solo se il prodotto è già acquistabile (in stock, oppure esaurito con backorder abilitati da WooCommerce). Il cliente compra subito, senza lasciare l'email.
    </p>

    <script>
    (function() {
        var cb = document.querySelector('input[name="lmpo_enabled"]');
        var fields = document.getElementById('lmpo-fields');
        cb.addEventListener('change', function() {
            fields.style.opacity = this.checked ? '1' : '.4';
            fields.style.pointerEvents = this.checked ? 'auto' : 'none';
        });
    })();
    </script>
    <?php
}

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — SALVATAGGIO
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'save_post_product', function ( int $post_id ) {
    if ( ! isset( $_POST['lmpo_nonce'] ) || ! wp_verify_nonce( $_POST['lmpo_nonce'], 'lmpo_save' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_product', $post_id ) ) return;

    $enabled = isset( $_POST['lmpo_enabled'] ) ? '1' : '';
    update_post_meta( $post_id, LMPO_PREFIX . 'enabled', $enabled );
    update_post_meta( $post_id, LMPO_PREFIX . 'discount_type', sanitize_text_field( $_POST['lmpo_discount_type'] ?? 'percentage' ) );
    update_post_meta( $post_id, LMPO_PREFIX . 'discount_value', (float) ( $_POST['lmpo_discount_value'] ?? 0 ) );
    update_post_meta( $post_id, LMPO_PREFIX . 'note', sanitize_textarea_field( $_POST['lmpo_note'] ?? '' ) );
    update_post_meta( $post_id, LMPO_PREFIX . 'date', sanitize_text_field( $_POST['lmpo_date'] ?? '' ) );
    update_post_meta( $post_id, LMPO_PREFIX . 'badge_text', sanitize_text_field( $_POST['lmpo_badge_text'] ?? 'Disponibile in preordine' ) );

}, 20 );

// ─────────────────────────────────────────────────────────────────────────────
// NOTA — questo plugin NON forza la disponibilità del prodotto.
// Applica solo badge, sconto e testo "Preordina": se il prodotto è esaurito
// e senza backorder abilitati in WooCommerce, resta non acquistabile come
// da comportamento standard. Per venderlo comunque da esaurito, abilita i
// backorder sulla scheda prodotto (Inventario → Backorder → "Consenti, ma
// notifica al cliente" o "Consenti").
// ─────────────────────────────────────────────────────────────────────────────

// ─────────────────────────────────────────────────────────────────────────────
// ADMIN — COLONNA NELL'ELENCO PRODOTTI
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'manage_edit-product_columns', function ( array $columns ): array {
    $columns['lmpo_preorder'] = '🕒 Preordina';
    return $columns;
} );

add_action( 'manage_product_posts_custom_column', function ( string $column, int $post_id ) {
    if ( $column !== 'lmpo_preorder' ) return;
    if ( get_post_meta( $post_id, LMPO_PREFIX . 'enabled', true ) === '1' ) {
        echo '<span style="color:#b88a3e;font-weight:600;">● Attivo</span>';
    } else {
        echo '<span style="color:#ccc;">—</span>';
    }
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// HELPER — calcolo prezzo scontato
// ─────────────────────────────────────────────────────────────────────────────

function lmpo_is_enabled( $product_id ): bool {
    return get_post_meta( $product_id, LMPO_PREFIX . 'enabled', true ) === '1';
}

// Come lmpo_is_enabled(), ma su un oggetto prodotto/variazione: se è una
// variazione, il preordine è impostato sul prodotto "padre" (id diverso
// dalla variazione stessa), quindi controlla anche quello.
function lmpo_is_enabled_for( $product ): bool {
    if ( ! $product ) return false;
    if ( lmpo_is_enabled( $product->get_id() ) ) return true;
    if ( is_callable( array( $product, 'get_parent_id' ) ) && $product->get_parent_id() ) {
        return lmpo_is_enabled( $product->get_parent_id() );
    }
    return false;
}

// Prezzo di riferimento su cui calcolare lo sconto: sul prodotto variabile
// (es. formati/varianti diverse) get_regular_price() del "padre" è vuoto —
// ogni variante ha il proprio prezzo. Usiamo il prezzo minimo tra le
// varianti come base per mostrare lo sconto sulla pagina prodotto.
function lmpo_get_reference_price( $product ): float {
    if ( ! $product ) return 0.0;
    if ( $product->is_type( 'variable' ) ) {
        $min = $product->get_variation_regular_price( 'min', true );
        return $min !== '' ? (float) $min : 0.0;
    }
    return (float) $product->get_regular_price();
}

function lmpo_get_discounted_price( float $original_price, int $product_id ): float {
    $type  = get_post_meta( $product_id, LMPO_PREFIX . 'discount_type', true ) ?: 'percentage';
    $value = (float) get_post_meta( $product_id, LMPO_PREFIX . 'discount_value', true );

    if ( $value <= 0 ) return $original_price;

    if ( $type === 'percentage' ) {
        $discounted = $original_price - ( $original_price * $value / 100 );
    } else {
        $discounted = $original_price - $value;
    }

    return max( 0, round( $discounted, 2 ) );
}

function lmpo_get_discount_label( int $product_id ): string {
    $type  = get_post_meta( $product_id, LMPO_PREFIX . 'discount_type', true ) ?: 'percentage';
    $value = (float) get_post_meta( $product_id, LMPO_PREFIX . 'discount_value', true );

    if ( $value <= 0 ) return '';

    return $type === 'percentage'
        ? '-' . rtrim( rtrim( number_format( $value, 1, ',', '' ), '0' ), ',' ) . '%'
        : '-' . wc_price( $value );
}

// ─────────────────────────────────────────────────────────────────────────────
// FRONTEND — testo pulsante "Aggiungi al carrello" → "Preordina"
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'woocommerce_product_add_to_cart_text', 'lmpo_button_text', 10, 2 );
add_filter( 'woocommerce_product_single_add_to_cart_text', 'lmpo_button_text', 10, 2 );

function lmpo_button_text( string $text, $product ): string {
    if ( $product && lmpo_is_enabled( $product->get_id() ) ) {
        return 'Preordina ora';
    }
    return $text;
}

// ─────────────────────────────────────────────────────────────────────────────
// FRONTEND — riquadro sotto il pulsante nella pagina prodotto
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'woocommerce_after_add_to_cart_button', function () {
    global $product;
    if ( ! $product || ! lmpo_is_enabled( $product->get_id() ) ) return;

    $badge = get_post_meta( $product->get_id(), LMPO_PREFIX . 'badge_text', true ) ?: 'Disponibile in preordine';
    $note  = get_post_meta( $product->get_id(), LMPO_PREFIX . 'note', true );
    $date  = get_post_meta( $product->get_id(), LMPO_PREFIX . 'date', true );

    // Etichetta sconto: dipende solo dal valore impostato nel pannello, non
    // dal prezzo del prodotto — sempre visibile, anche sui prodotti
    // variabili dove il prezzo "di riferimento" è meno immediato da calcolare.
    // Il prezzo scontato vero e proprio si vede in carrello/checkout.
    $discount_label = lmpo_get_discount_label( $product->get_id() );

    echo '<div class="lmpo-box">';
    echo '<div class="lmpo-badge">🕒 ' . esc_html( $badge ) . '</div>';

    if ( $discount_label ) {
        echo '<div class="lmpo-price"><span class="lmpo-price-new">Sconto preordine: ' . esc_html( $discount_label ) . '</span></div>';
    }

    if ( $note )  echo '<p class="lmpo-note">' . esc_html( $note ) . '</p>';
    if ( $date )  echo '<p class="lmpo-date">📅 Disponibilità prevista: <strong>' . esc_html( $date ) . '</strong></p>';

    echo '</div>';
} );

// CSS del riquadro e del tag prezzo, su tutte le pagine WooCommerce rilevanti
add_action( 'wp_head', function () {
    $is_relevant = function_exists( 'is_product' ) && (
        is_product() ||
        ( function_exists( 'is_shop' ) && is_shop() ) ||
        ( function_exists( 'is_product_category' ) && is_product_category() ) ||
        ( function_exists( 'is_cart' ) && is_cart() ) ||
        ( function_exists( 'is_checkout' ) && is_checkout() )
    );
    if ( ! $is_relevant ) return;
    ?>
    <style>
    .lmpo-discount-tag-inline {
        display: inline-block;
        background: #c0392b;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 10px;
        vertical-align: middle;
        margin-left: 4px;
    }
    .lmpo-box {
        margin: 14px 0 18px;
        padding: 14px 16px;
        background: linear-gradient(135deg,#fdf9f0,#faf5e8);
        border: 1px solid #e8d5a3;
        border-radius: 8px;
    }
    .lmpo-badge {
        display: inline-block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: #8a5c00;
        background: #fff3d6;
        border: 1px solid #e8c56a;
        border-radius: 20px;
        padding: 4px 12px;
        margin-bottom: 8px;
    }
    .lmpo-price {
        font-size: 18px;
        margin: 10px 0;
        padding: 8px 12px;
        background: #fff;
        border: 1px solid #e8c56a;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .lmpo-price-old { text-decoration: line-through; color: #999; font-size: 14px; }
    .lmpo-price-new { color: #8a5c00; font-weight: 800; font-size: 20px; }
    .lmpo-discount-tag {
        display: inline-block;
        background: #c0392b;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 10px;
        vertical-align: middle;
    }
    .lmpo-note { font-size: 13px; color: #5a4010; margin: 6px 0 0; }
    .lmpo-date { font-size: 13px; color: #5a4010; margin: 4px 0 0; }
    </style>
    <?php
} );

// ─────────────────────────────────────────────────────────────────────────────
// FRONTEND — applica lo sconto al prezzo mostrato
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'woocommerce_get_price_html', function ( string $html, $product ) {
    if ( ! $product || ! lmpo_is_enabled( $product->get_id() ) ) return $html;

    $original   = lmpo_get_reference_price( $product );
    $discounted = lmpo_get_discounted_price( $original, $product->get_id() );

    if ( $discounted >= $original || $original <= 0 ) return $html;

    $label = lmpo_get_discount_label( $product->get_id() );
    $tag   = $label ? ' <span class="lmpo-discount-tag-inline">' . $label . '</span>' : '';

    return '<del>' . wc_price( $original ) . '</del> <ins>' . wc_price( $discounted ) . '</ins>' . $tag;
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// CARRELLO / CHECKOUT — applica lo sconto reale al totale
// ─────────────────────────────────────────────────────────────────────────────

add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return; // evita doppio calcolo

    foreach ( $cart->get_cart() as $item ) {
        $product = $item['data'];
        if ( ! lmpo_is_enabled( $product->get_id() ) ) continue;

        $original   = (float) $product->get_regular_price();
        $discounted = lmpo_get_discounted_price( $original, $product->get_id() );

        if ( $discounted > 0 && $discounted < $original ) {
            $product->set_price( $discounted );
        }
    }
}, 20, 1 );

// Prezzo unitario mostrato nella pagina carrello (colonna "Prezzo")
add_filter( 'woocommerce_cart_item_price', function ( string $price_html, array $cart_item, string $cart_item_key ) {
    $product = $cart_item['data'];
    if ( ! lmpo_is_enabled( $product->get_id() ) ) return $price_html;

    $original   = (float) $product->get_regular_price();
    $discounted = lmpo_get_discounted_price( $original, $product->get_id() );
    if ( $discounted >= $original || $original <= 0 ) return $price_html;

    $label = lmpo_get_discount_label( $product->get_id() );
    $tag   = $label ? ' <span class="lmpo-discount-tag-inline">' . $label . '</span>' : '';

    return '<del>' . wc_price( $original ) . '</del> <ins>' . wc_price( $discounted ) . '</ins>' . $tag;
}, 10, 3 );

// Subtotale (prezzo × quantità) mostrato in carrello e nel riepilogo checkout
add_filter( 'woocommerce_cart_item_subtotal', function ( string $subtotal_html, array $cart_item, string $cart_item_key ) {
    $product = $cart_item['data'];
    if ( ! lmpo_is_enabled( $product->get_id() ) ) return $subtotal_html;

    $original   = (float) $product->get_regular_price();
    $discounted = lmpo_get_discounted_price( $original, $product->get_id() );
    if ( $discounted >= $original || $original <= 0 ) return $subtotal_html;

    $qty = (int) $cart_item['quantity'];

    return '<del>' . wc_price( $original * $qty ) . '</del> <ins>' . wc_price( $discounted * $qty ) . '</ins>';
}, 10, 3 );

// Etichetta "Preordine" accanto al nome prodotto nel carrello/checkout
add_filter( 'woocommerce_cart_item_name', function ( string $name, array $cart_item ) {
    if ( lmpo_is_enabled( $cart_item['product_id'] ) ) {
        $date = get_post_meta( $cart_item['product_id'], LMPO_PREFIX . 'date', true );
        $label = 'PREORDINE' . ( $date ? ' · consegna prevista ' . esc_html( $date ) : '' );
        $name .= ' <span style="display:inline-block;margin-left:6px;padding:2px 8px;background:#fff3d6;border:1px solid #e8c56a;border-radius:12px;font-size:11px;font-weight:700;color:#8a5c00;">' . $label . '</span>';
    }
    return $name;
}, 10, 2 );

// ─────────────────────────────────────────────────────────────────────────────
// BADGE nello shop / archivio prodotti (al posto di "Esaurito")
// ─────────────────────────────────────────────────────────────────────────────

add_filter( 'woocommerce_sale_flash', function ( string $html, $post, $product ) {
    if ( $product && lmpo_is_enabled( $product->get_id() ) ) {
        $label = lmpo_get_discount_label( $product->get_id() );
        $text  = $label ? 'Preordina ' . $label : 'Preordina';
        return '<span class="onsale lmpo-flash">' . $text . '</span>';
    }
    return $html;
}, 10, 3 );

add_action( 'wp_head', function () {
    ?>
    <style>.lmpo-flash{background:#b88a3e !important;}</style>
    <?php
} );
