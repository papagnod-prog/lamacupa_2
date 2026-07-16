<?php
/**
 * Template pagina Shop.
 *
 * Se WooCommerce è attivo mostra i prodotti reali (con aggiunta al carrello
 * via AJAX → apre il drawer laterale). Altrimenti mostra le schede dimostrative.
 *
 * @package Lamacupa
 */
get_header();

$lmcp_has_woo = class_exists( 'WooCommerce' );
?>

<div class="shopbar">
  <div class="wrap">
    <span class="eyebrow" data-en="The Shop">Lo Shop</span>
    <h1 data-en="All our products">Tutti i nostri prodotti</h1>
    <div class="chips">
      <?php
      if ( $lmcp_has_woo ) {
        $current_cat = isset( $_GET['product_cat'] ) ? sanitize_title( wp_unslash( $_GET['product_cat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
        $shop_url    = get_permalink();
        printf(
          '<a class="chip%s" href="%s">%s</a>',
          '' === $current_cat ? ' active' : '',
          esc_url( $shop_url ),
          esc_html__( 'Tutti', 'lamacupa' )
        );
        $cats = get_terms( array(
          'taxonomy'   => 'product_cat',
          'hide_empty' => true,
          'number'     => 6,
        ) );
        if ( ! is_wp_error( $cats ) ) {
          foreach ( $cats as $cat ) {
            printf(
              '<a class="chip%s" href="%s">%s</a>',
              $current_cat === $cat->slug ? ' active' : '',
              esc_url( add_query_arg( 'product_cat', $cat->slug, $shop_url ) ),
              esc_html( $cat->name )
            );
          }
        }
      } else {
        ?>
        <span class="chip active" data-en="All">Tutti</span>
        <span class="chip" data-en="Luma oil">Olio Luma</span>
        <span class="chip" data-en="The Jars">Gli Orci</span>
        <span class="chip" data-en="Gift boxes">Confezioni regalo</span>
        <?php
      }
      ?>
    </div>
  </div>
</div>

<section class="block" style="padding-top:56px">
  <div class="wrap">
    <div class="cards">
      <?php if ( $lmcp_has_woo ) :
        $paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
        $args  = array(
          'post_type'      => 'product',
          'post_status'    => 'publish',
          'posts_per_page' => 12,
          'paged'          => $paged,
        );
        if ( ! empty( $current_cat ) ) {
          $args['tax_query'] = array( array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => $current_cat,
          ) );
        }
        $loop = new WP_Query( $args );
        if ( $loop->have_posts() ) :
          while ( $loop->have_posts() ) : $loop->the_post();
            global $product;
            if ( ! $product ) { continue; }
            $cats = wp_get_post_terms( get_the_ID(), 'product_cat', array( 'fields' => 'names' ) );
            $cat_label = ( ! is_wp_error( $cats ) && $cats ) ? $cats[0] : '';
            $ajax_ok = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
            ?>
            <div class="card">
              <a href="<?php the_permalink(); ?>" class="card-media">
                <?php if ( $product->is_on_sale() ) : ?><span class="card-tag" data-en="Sale">Offerta</span><?php endif; ?>
                <?php
                if ( has_post_thumbnail() ) {
                  the_post_thumbnail( 'woocommerce_thumbnail', array( 'style' => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover' ) );
                } else {
                  echo '<div class="imgslot"></div>';
                }
                ?>
              </a>
              <div class="card-body">
                <h4><?php the_title(); ?></h4>
                <div class="ct"><?php echo esc_html( $cat_label ); ?></div>
                <div class="card-foot">
                  <span class="p"><?php echo wp_kses_post( $product->get_price_html() ); ?></span>
                  <?php if ( $ajax_ok ) : ?>
                    <a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
                       data-quantity="1"
                       data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                       class="add ajax_add_to_cart add_to_cart_button"
                       rel="nofollow"><span data-en="+ Cart">+ Carrello</span></a>
                  <?php else : ?>
                    <a class="add" href="<?php the_permalink(); ?>" data-en="View">Vedi</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php
          endwhile;
        else : ?>
          <p style="grid-column:1/-1;color:var(--ink-soft)" data-en="No products found.">Nessun prodotto trovato.</p>
        <?php
        endif;
        wp_reset_postdata();
      else :
        /* ---- Fallback dimostrativo (WooCommerce non attivo) ---- */
        ?>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/olio/' ) ); ?>" class="card-media"><span class="card-tag" data-en="Best seller">Best seller</span><div class="imgslot" id="a-p1"></div></a>
          <div class="card-body"><h4>Luma 250ml</h4><div class="ct" data-en="Coratina monovarietal">Coratina monovarietale</div>
            <div class="card-foot"><span class="p">€12,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/olio/' ) ); ?>" class="card-media"><div class="imgslot" id="a-p2"></div></a>
          <div class="card-body"><h4>Luma 500ml</h4><div class="ct" data-en="Coratina monovarietal">Coratina monovarietale</div>
            <div class="card-foot"><span class="p">€19,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/olio/' ) ); ?>" class="card-media"><span class="card-tag" data-en="To taste">Da assaggio</span><div class="imgslot" id="shop-100"></div></a>
          <div class="card-body"><h4>Luma 100ml</h4><div class="ct" data-en="Coratina monovarietal">Coratina monovarietale</div>
            <div class="card-foot"><span class="p">€6,50</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/olio/' ) ); ?>" class="card-media"><div class="imgslot" id="a-p4"></div></a>
          <div class="card-body"><h4 data-en="Tasting box">Confezione Degustazione</h4><div class="ct">3 × 100ml</div>
            <div class="card-foot"><span class="p">€22,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/orci/' ) ); ?>" class="card-media"><span class="card-tag" data-en="Gift idea">Idea regalo</span><div class="imgslot" id="a-p3"></div></a>
          <div class="card-body"><h4 data-en="Terracotta jar 500ml">Orcio in terracotta 500ml</h4><div class="ct" data-en="Special edition">Edizione speciale</div>
            <div class="card-foot"><span class="p">€34,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="card-media"><div class="imgslot" id="shop-gift2"></div></a>
          <div class="card-body"><h4 data-en="2-bottle gift box">Confezione regalo 2 bottiglie</h4><div class="ct">2 × 250ml Luma</div>
            <div class="card-foot"><span class="p">€28,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="card-media"><div class="imgslot" id="shop-gift3"></div></a>
          <div class="card-body"><h4 data-en="3-bottle gift box">Confezione regalo 3 bottiglie</h4><div class="ct">3 × 250ml Luma</div>
            <div class="card-foot"><span class="p">€39,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
        <div class="card">
          <a href="<?php echo esc_url( home_url( '/orci/' ) ); ?>" class="card-media"><div class="imgslot" id="orci-3"></div></a>
          <div class="card-body"><h4 data-en="Large jar · 1L">Orcio grande · 1L</h4><div class="ct" data-en="Glazed terracotta">Terracotta smaltata</div>
            <div class="card-foot"><span class="p">€52,00</span><button class="add" data-add-cart style="background:none;border:none;font-family:inherit;cursor:pointer"><span data-add-label data-en="+ Cart">+ Carrello</span></button></div></div>
        </div>
      <?php endif; ?>
    </div>

    <?php
    if ( $lmcp_has_woo && isset( $loop ) && $loop->max_num_pages > 1 ) {
      echo '<div class="shop-pagination" style="margin-top:40px;text-align:center">';
      echo wp_kses_post( paginate_links( array(
        'total'   => $loop->max_num_pages,
        'current' => $paged,
      ) ) );
      echo '</div>';
    }
    ?>
  </div>
</section>

<!-- reassurance -->
<div class="trust">
  <div class="wrap"><div class="trust-grid">
    <div class="trust-item"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M3 7h13v8H3z"/><path d="M16 10h3l2 2v3h-5z"/><circle cx="7" cy="17" r="1.6"/><circle cx="17" cy="17" r="1.6"/></svg><h4 data-en="Free shipping">Spedizione gratuita</h4><p data-en="In Italy over €59">In Italia oltre 59 €</p></div>
    <div class="trust-item"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke-width="1.5"><rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/></svg><h4 data-en="Secure payments">Pagamenti sicuri</h4><p data-en="Credit card &amp; PayPal">Carta di credito e PayPal</p></div>
    <div class="trust-item"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M12 3C7 8 6 12 9 16c2 2.5 4 2.5 6 0 3-4 2-8-3-13Z"/></svg><h4 data-en="Cold extraction">Estrazione a freddo</h4><p data-en="The fresh harvest">Dal raccolto fresco</p></div>
    <div class="trust-item"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke-width="1.5"><path d="M21 11.5a8.5 8.5 0 1 1-5-7.7"/><path d="M9 11l3 3 7-8"/></svg><h4 data-en="From the grower">Dal produttore</h4><p data-en="Short chain, zero km">Filiera corta, km 0</p></div>
  </div></div>
</div>

<?php get_footer(); ?>
