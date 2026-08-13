<?php
/**
 * Plugin Name: Lamacupa Premi
 * Description: Gestione premi e riconoscimenti con loghi. Visualizzati sulle schede prodotto WooCommerce. Pannello admin completo con banner urgenza configurabile.
 * Version:     3.0.0
 * Author:      Simply APP / Origine Digitale
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LMCP_OPT_PREMI',  'lmcp_premi_v3' );
define( 'LMCP_OPT_BANNER', 'lmcp_banner_v3' );
define( 'LMCP_OPT_LOGOSIZE', 'lmcp_logo_size_v3' );

// ─── HELPERS ─────────────────────────────────────────────────────────────────

function lmcp_get_premi(): array {
    $d = get_option( LMCP_OPT_PREMI, [] );
    return is_array( $d ) ? $d : [];
}

function lmcp_get_banner(): array {
    $def = [ 'attivo' => false, 'icona' => '⏳', 'testo' => 'Ultima disponibilità raccolta 2025 — nuova raccolta prevista novembre 2026.' ];
    $saved = get_option( LMCP_OPT_BANNER, [] );
    return array_merge( $def, is_array( $saved ) ? $saved : [] );
}

function lmcp_get_logo_size(): int {
    $size = (int) get_option( LMCP_OPT_LOGOSIZE, 56 );
    return $size > 0 ? $size : 56;
}

// ─── MENU ────────────────────────────────────────────────────────────────────

add_action( 'admin_menu', function () {
    add_options_page( 'Lamacupa Premi', '🏅 Premi & Loghi', 'manage_options', 'lamacupa-premi', 'lmcp_page' );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
    if ( $hook === 'settings_page_lamacupa-premi' ) wp_enqueue_media();
} );

// ─── SALVATAGGIO via admin-post.php ──────────────────────────────────────────

add_action( 'admin_post_lmcp_save', function () {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Permesso negato' );
    check_admin_referer( 'lmcp_nonce' );

    // Premi
    $nomi     = (array)( $_POST['lmcp_nome']    ?? [] );
    $anni     = (array)( $_POST['lmcp_anno']    ?? [] );
    $descs    = (array)( $_POST['lmcp_desc']    ?? [] );
    $logo_ids = (array)( $_POST['lmcp_logo_id'] ?? [] );
    $ordini   = (array)( $_POST['lmcp_ordine']  ?? [] );

    $premi = [];
    foreach ( $nomi as $i => $nome ) {
        $nome = sanitize_text_field( $nome );
        if ( $nome === '' ) continue;
        $lid = (int)( $logo_ids[$i] ?? 0 );
        $premi[] = [
            'nome'     => $nome,
            'anno'     => sanitize_text_field( $anni[$i] ?? '' ),
            'desc'     => sanitize_textarea_field( $descs[$i] ?? '' ),
            'logo_id'  => $lid,
            'logo_url' => $lid ? (string) wp_get_attachment_url( $lid ) : '',
            'ordine'   => (int)( $ordini[$i] ?? $i ),
        ];
    }
    usort( $premi, fn( $a, $b ) => $a['ordine'] <=> $b['ordine'] );
    update_option( LMCP_OPT_PREMI, $premi );

    // Banner
    update_option( LMCP_OPT_BANNER, [
        'attivo' => isset( $_POST['lmcp_banner_on'] ),
        'icona'  => sanitize_text_field( $_POST['lmcp_banner_icona'] ?? '⏳' ),
        'testo'  => sanitize_textarea_field( $_POST['lmcp_banner_testo'] ?? '' ),
    ] );

    $logo_size = (int) ( $_POST['lmcp_logo_size'] ?? 56 );
    $logo_size = max( 20, min( 200, $logo_size ) );
    update_option( LMCP_OPT_LOGOSIZE, $logo_size );

    wp_safe_redirect( admin_url( 'options-general.php?page=lamacupa-premi&ok=1' ) );
    exit;
} );

// ─── PAGINA ADMIN ─────────────────────────────────────────────────────────────

function lmcp_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) return;
    $premi  = lmcp_get_premi();
    $banner = lmcp_get_banner();
    ?>
    <style>
    .lmcp-row{background:#fff;border:1px solid #ddd;border-radius:8px;padding:14px;margin-bottom:10px;
              display:grid;grid-template-columns:1fr 75px 140px 1fr 65px 38px;gap:10px;align-items:start;}
    .lmcp-row label{font-size:11px;font-weight:700;color:#888;display:block;margin-bottom:3px;text-transform:uppercase;letter-spacing:.04em;}
    .lmcp-row input[type=text],.lmcp-row textarea{width:100%;box-sizing:border-box;}
    .lmcp-img{max-height:40px;max-width:90px;object-fit:contain;display:block;margin-top:4px;border:1px solid #eee;border-radius:4px;padding:2px;}
    .lmcp-del-btn{background:none;border:none;color:#c0392b;cursor:pointer;font-size:20px;padding:2px;margin-top:20px;}
    </style>

    <div class="wrap" style="max-width:920px;">
        <h1>🏅 Premi & Riconoscimenti — Lamacupa</h1>
        <p style="color:#666;">Aggiungi i premi con i loghi originali. Vengono visualizzati sulle schede prodotto WooCommerce sopra "Aggiungi al carrello".</p>

        <?php if ( isset( $_GET['ok'] ) ) : ?>
            <div class="notice notice-success is-dismissible" style="padding:10px 14px;">✅ <strong>Salvato con successo!</strong></div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="lmcp_save">
            <?php wp_nonce_field( 'lmcp_nonce' ); ?>

            <!-- PREMI -->
            <h2 style="margin-top:20px;">🏅 Premi</h2>
            <div id="lmcp-list">
                <?php foreach ( $premi as $i => $p ) lmcp_row( $i, $p ); ?>
                <?php if ( empty( $premi ) ) echo '<p style="color:#999;font-style:italic;">Nessun premio inserito. Clicca il pulsante qui sotto.</p>'; ?>
            </div>
            <button type="button" id="lmcp-add" class="button" style="margin:10px 0 28px;">➕ Aggiungi premio</button>

            <hr>

            <!-- BANNER -->
            <h2>⏳ Banner urgenza / stagionalità</h2>
            <p style="color:#666;font-size:13px;">Mostra un messaggio evidenziato sulla scheda prodotto — disponibilità limitata, pre-ordini, stagionalità.</p>

            <table class="form-table" style="max-width:680px;">
                <tr>
                    <th>Abilita banner</th>
                    <td>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                            <input type="checkbox" name="lmcp_banner_on" value="1" <?php checked( $banner['attivo'] ); ?> style="width:18px;height:18px;">
                            Mostra il banner sulle schede prodotto
                        </label>
                    </td>
                </tr>
                <tr>
                    <th>Icona</th>
                    <td>
                        <input type="text" name="lmcp_banner_icona" id="bi" value="<?php echo esc_attr( $banner['icona'] ); ?>" style="width:60px;font-size:18px;text-align:center;">
                        <span style="color:#888;font-size:12px;margin-left:6px;">Emoji o testo breve</span>
                    </td>
                </tr>
                <tr>
                    <th>Testo</th>
                    <td>
                        <textarea name="lmcp_banner_testo" id="bt" rows="2" style="width:100%;max-width:480px;"><?php echo esc_textarea( $banner['testo'] ); ?></textarea>
                        <p class="description">Scrivi **parola** per metterla in <strong>grassetto</strong>.</p>
                    </td>
                </tr>
                <tr>
                    <th>Anteprima</th>
                    <td>
                        <div style="background:#fff8e6;border-left:3px solid #b88a3e;padding:10px 14px;border-radius:0 6px 6px 0;display:inline-flex;align-items:center;gap:8px;max-width:480px;">
                            <span id="pi" style="font-size:18px;"><?php echo esc_html( $banner['icona'] ); ?></span>
                            <span id="pt" style="color:#5a4010;font-size:13px;"><?php echo esc_html( $banner['testo'] ); ?></span>
                        </div>
                    </td>
                </tr>
            </table>

            <hr>

            <!-- DIMENSIONE LOGHI HOMEPAGE -->
            <h2>📐 Dimensione loghi — modalità solo icone</h2>
            <p style="color:#666;font-size:13px;">Controlla la grandezza dei loghi usati con <code>[lamacupa_premi mode="logo"]</code> (es. in homepage). Su smartphone si riducono automaticamente in proporzione.</p>

            <table class="form-table" style="max-width:680px;">
                <tr>
                    <th style="width:160px;">Dimensione (px)</th>
                    <td>
                        <input type="range" name="lmcp_logo_size" id="ls-range" min="30" max="120" step="2"
                               value="<?php echo esc_attr( lmcp_get_logo_size() ); ?>"
                               style="width:260px;vertical-align:middle;">
                        <span id="ls-val" style="font-weight:600;margin-left:10px;"><?php echo esc_html( lmcp_get_logo_size() ); ?>px</span>
                    </td>
                </tr>
                <tr>
                    <th>Anteprima</th>
                    <td>
                        <div style="background:#fdf9f0;border:1px solid #e8d5a3;border-radius:8px;padding:16px;display:flex;gap:14px;align-items:center;max-width:400px;">
                            <?php foreach ( array_slice( $premi, 0, 4 ) as $p ) : if ( empty( $p['logo_url'] ) ) continue; ?>
                                <img src="<?php echo esc_url($p['logo_url']); ?>" id="ls-preview-img"
                                     class="ls-preview-logo"
                                     style="height:<?php echo (int) lmcp_get_logo_size(); ?>px;width:<?php echo (int) lmcp_get_logo_size(); ?>px;object-fit:contain;">
                            <?php endforeach; ?>
                            <?php if ( empty( array_filter( $premi, fn($p) => !empty($p['logo_url']) ) ) ) : ?>
                                <span style="color:#999;font-size:13px;font-style:italic;">Carica almeno un logo per vedere l'anteprima</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            </table>

            <hr style="margin:24px 0;">

            <button type="submit" class="button button-primary" style="font-size:15px;padding:8px 30px;height:auto;">
                💾 Salva tutto
            </button>
        </form>

        <!-- ANTEPRIMA -->
        <?php if ( ! empty( $premi ) ) : ?>
        <hr style="margin:30px 0 20px;">
        <h2>👁 Anteprima badge</h2>
        <?php lmcp_css(); ?>
        <div style="background:#fdf9f0;border:1px solid #e8d5a3;border-radius:8px;padding:16px;max-width:600px;">
            <?php echo lmcp_html(); ?>
        </div>
        <?php endif; ?>

        <!-- SHORTCODE -->
        <hr style="margin:28px 0 20px;">
        <h2>📋 Shortcode</h2>
        <table class="widefat" style="max-width:460px;">
            <tr><td><code>[lamacupa_premi]</code></td><td>Badge completi con titolo</td></tr>
            <tr><td><code>[lamacupa_premi mini="yes"]</code></td><td>Badge compatti — sidebar/footer</td></tr>
        </table>
    </div>

    <!-- Template riga — FUORI dal form, cosi i campi required nascosti non bloccano l'invio -->
    <div id="lmcp-tpl" style="display:none;"><?php lmcp_row( 'XX', [] ); ?></div>

    <script>
    (function($){
        var n = <?php echo count( $premi ); ?>;

        // Aggiungi
        $('#lmcp-add').on('click', function(){
            var h = $('#lmcp-tpl').html().replace(/XX/g, n++);
            $('#lmcp-list').find('p').remove();
            $('#lmcp-list').append(h);
            bindRow($('#lmcp-list .lmcp-row').last());
        });

        // Rimuovi
        $(document).on('click','.lmcp-del-btn',function(){
            if(confirm('Rimuovere questo premio?')) $(this).closest('.lmcp-row').remove();
        });

        // Media Library
        function bindRow($r){
            $r.find('.lmcp-pick').off('click').on('click',function(e){
                e.preventDefault();
                var $row = $(this).closest('.lmcp-row');
                var frame = wp.media({
                    title: 'Seleziona logo',
                    button: { text: 'Usa questo logo' },
                    multiple: false,
                    library: { type: 'image' }
                });
                frame.on('select', function(){
                    var a = frame.state().get('selection').first().toJSON();
                    $row.find('.lmcp-logo-id').val(a.id);
                    $row.find('.lmcp-img').attr('src', a.url).show();
                    $row.find('.lmcp-logo-note').text('✅ Selezionato').css('color','#27ae60');
                });
                frame.open();
            });
            $r.find('.lmcp-clr').off('click').on('click',function(){
                var $row = $(this).closest('.lmcp-row');
                $row.find('.lmcp-logo-id').val('');
                $row.find('.lmcp-img').hide();
                $row.find('.lmcp-logo-note').text('Nessun logo').css('color','#999');
            });
        }

        $('#lmcp-list .lmcp-row').each(function(){ bindRow($(this)); });

        // Ordine prima submit
        $('form').on('submit',function(){
            $('#lmcp-list .lmcp-row').each(function(i){ $(this).find('.lmcp-ord').val(i); });
        });

        // Preview banner live
        $('#bi').on('input',function(){ $('#pi').text($(this).val()); });
        $('#bt').on('input',function(){ $('#pt').text($(this).val()); });

        // Preview slider dimensione loghi
        $('#ls-range').on('input',function(){
            var v = $(this).val();
            $('#ls-val').text(v + 'px');
            $('.ls-preview-logo').css({height:v+'px', width:v+'px'});
        });
    })(jQuery);
    </script>
    <?php
}

function lmcp_row( $i, array $p ): void {
    $lid = (int)( $p['logo_id'] ?? 0 );
    $lurl = $p['logo_url'] ?? '';
    ?>
    <div class="lmcp-row">
        <div>
            <label>Nome premio *</label>
            <input type="text" name="lmcp_nome[<?php echo $i; ?>]" value="<?php echo esc_attr($p['nome']??''); ?>" placeholder="es. Bibenda 5 Gocce" required>
        </div>
        <div>
            <label>Anno</label>
            <input type="text" name="lmcp_anno[<?php echo $i; ?>]" value="<?php echo esc_attr($p['anno']??''); ?>" placeholder="2024">
        </div>
        <div>
            <label>Logo</label>
            <input type="hidden" name="lmcp_logo_id[<?php echo $i; ?>]" class="lmcp-logo-id" value="<?php echo esc_attr($lid); ?>">
            <div style="display:flex;gap:4px;margin-bottom:4px;">
                <button type="button" class="button button-small lmcp-pick">📁 Scegli</button>
                <button type="button" class="button button-small lmcp-clr">✕</button>
            </div>
            <img src="<?php echo esc_url($lurl); ?>" class="lmcp-img" <?php if(!$lurl) echo 'style="display:none;"'; ?>>
            <span class="lmcp-logo-note" style="font-size:11px;color:<?php echo $lurl?'#27ae60':'#999'; ?>;">
                <?php echo $lurl ? '✅ Selezionato' : 'Nessun logo'; ?>
            </span>
        </div>
        <div>
            <label>Descrizione (tooltip)</label>
            <textarea name="lmcp_desc[<?php echo $i; ?>]" rows="2" placeholder="Descrizione breve..."><?php echo esc_textarea($p['desc']??''); ?></textarea>
        </div>
        <div>
            <label>Ordine</label>
            <input type="number" name="lmcp_ordine[<?php echo $i; ?>]" class="lmcp-ord" value="<?php echo esc_attr($p['ordine']??$i); ?>" style="width:55px;">
        </div>
        <div>
            <button type="button" class="lmcp-del-btn" title="Rimuovi">🗑</button>
        </div>
    </div>
    <?php
}

// ─── FRONTEND ─────────────────────────────────────────────────────────────────

function lmcp_css(): void {
    $size = lmcp_get_logo_size();
    // Responsive: su mobile (viewport stretto) scala fino al 55% del valore impostato, mai sotto 26px
    $min = max( 26, (int) round( $size * 0.55 ) );
    ?>
<style>
:root{--lmcp-logo-size:clamp(<?php echo $min; ?>px, <?php echo round($size/6,1); ?>vw, <?php echo $size; ?>px);}
.lmcp-premi{display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:16px 0 20px;padding:14px 16px;
    background:linear-gradient(135deg,#fdf9f0,#faf5e8);border:1px solid #e8d5a3;border-radius:8px;}
.lmcp-premi-title{width:100%;font-size:11px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#8a7040;margin-bottom:4px;}
.lmcp-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 10px;background:#fff;border:1px solid #dcc87a;
    border-radius:20px;font-size:12px;font-weight:600;color:#5a4010;white-space:nowrap;cursor:default;
    transition:transform .15s,box-shadow .15s;min-height:32px;box-sizing:border-box;}
.lmcp-badge:hover{transform:translateY(-1px);box-shadow:0 3px 8px rgba(184,138,62,.2);}
.lmcp-badge img{height:22px !important;width:22px !important;max-width:22px !important;max-height:22px !important;
    min-width:22px !important;min-height:22px !important;object-fit:contain;object-position:center;
    display:block;flex-shrink:0;}
.lmcp-badge .lmcp-anno{font-size:10px;font-weight:400;color:#8a7040;}
.lmcp-urgenza{display:flex;align-items:center;gap:10px;margin:12px 0 18px;padding:10px 14px;
    background:#fff8e6;border-left:3px solid #b88a3e;border-radius:0 6px 6px 0;font-size:13px;color:#5a4010;}
.lmcp-premi-mini{display:flex;flex-wrap:wrap;gap:4px;margin:6px 0;}
.lmcp-premi-mini .lmcp-badge{font-size:10px;padding:3px 7px;min-height:24px;}
.lmcp-premi-mini .lmcp-badge img{height:16px !important;width:16px !important;max-width:16px !important;
    max-height:16px !important;min-width:16px !important;min-height:16px !important;}
.lmcp-premi-logo{display:flex;flex-wrap:wrap;gap:14px;align-items:center;}
.lmcp-badge-logo{display:inline-flex;align-items:center;justify-content:center;cursor:default;
    transition:transform .15s,opacity .15s;opacity:.85;}
.lmcp-badge-logo:hover{transform:translateY(-2px);opacity:1;}
.lmcp-badge-logo img{height:var(--lmcp-logo-size) !important;width:var(--lmcp-logo-size) !important;
    max-width:var(--lmcp-logo-size) !important;max-height:var(--lmcp-logo-size) !important;
    object-fit:contain;object-position:center;display:block;}
.lmcp-logo-fallback{font-size:calc(var(--lmcp-logo-size) * 0.7);line-height:1;}
</style>
<?php }

add_action( 'wp_head', function () {
    global $post;
    $has_shortcode = $post && has_shortcode( $post->post_content, 'lamacupa_premi' );
    if ( is_product() || is_shop() || is_product_category() || is_front_page() || is_home() || $has_shortcode ) {
        lmcp_css();
    }
} );

function lmcp_html( string $mode = 'full' ): string {
    $premi = lmcp_get_premi();
    if ( empty( $premi ) ) return '';

    $class = match ( $mode ) {
        'mini' => 'lmcp-premi-mini',
        'logo' => 'lmcp-premi-logo',
        default => 'lmcp-premi',
    };

    $out = '<div class="' . $class . '">';
    if ( $mode === 'full' ) $out .= '<div class="lmcp-premi-title">🏅 Riconoscimenti internazionali</div>';

    foreach ( $premi as $p ) {
        $tooltip = trim( $p['nome'] . ( $p['anno'] ? ' ' . $p['anno'] : '' ) . ( $p['desc'] ? ' — ' . $p['desc'] : '' ) );

        if ( $mode === 'logo' ) {
            $logo = $p['logo_url']
                ? '<img src="' . esc_url($p['logo_url']) . '" alt="' . esc_attr($p['nome']) . '" loading="lazy">'
                : '<span class="lmcp-logo-fallback">🏅</span>';
            $out .= '<span class="lmcp-badge-logo" title="' . esc_attr( $tooltip ) . '">' . $logo . '</span>';
            continue;
        }

        $logo = $p['logo_url'] ? '<img src="' . esc_url($p['logo_url']) . '" alt="' . esc_attr($p['nome']) . '" loading="lazy">' : '🏅';
        $anno = $p['anno'] ? '<span class="lmcp-anno">' . esc_html($p['anno']) . '</span>' : '';
        $out .= '<span class="lmcp-badge" title="' . esc_attr($p['desc']??'') . '">' . $logo . esc_html($p['nome']) . $anno . '</span>';
    }
    return $out . '</div>';
}

add_shortcode( 'lamacupa_premi', function ( $atts ) {
    $atts = shortcode_atts( [ 'mini' => 'no', 'mode' => '' ], $atts );
    // Retrocompatibilità: mini="yes" continua a funzionare
    if ( $atts['mode'] === '' ) {
        $atts['mode'] = $atts['mini'] === 'yes' ? 'mini' : 'full';
    }
    return lmcp_html( $atts['mode'] );
} );

add_action( 'woocommerce_before_add_to_cart_form', function () {
    $html = lmcp_html( 'full' );
    if ( $html ) echo $html;

    $b = lmcp_get_banner();
    if ( ! empty( $b['attivo'] ) && ! empty( $b['testo'] ) ) {
        $testo = preg_replace( '/\*\*(.+?)\*\*/', '<strong>$1</strong>', esc_html( $b['testo'] ) );
        echo '<div class="lmcp-urgenza">' . esc_html($b['icona']) . ' <span>' . $testo . '</span></div>';
    }
} );
