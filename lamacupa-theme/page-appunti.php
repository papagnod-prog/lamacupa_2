<?php
/**
 * Pagina "Appunti" — indice del BLOG (Articoli di WordPress).
 * Mostra l'articolo più recente in evidenza, poi la griglia, con filtri
 * per categoria e ricerca. Crea articoli da WordPress → Articoli.
 *
 * @package Lamacupa
 */
get_header();

$lc_cat = isset( $_GET['cat'] ) ? sanitize_title( wp_unslash( $_GET['cat'] ) ) : '';   // phpcs:ignore WordPress.Security.NonceVerification
$lc_q   = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';   // phpcs:ignore WordPress.Security.NonceVerification
$paged  = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );

$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 10, 'paged' => $paged );
if ( $lc_cat ) { $args['category_name'] = $lc_cat; }
if ( $lc_q ) { $args['s'] = $lc_q; }
$blog = new WP_Query( $args );
$base = get_permalink();
?>

<div class="shopbar" style="background:var(--paper);border-bottom:1px solid var(--line);padding:64px 0 40px">
  <div class="wrap">
    <span class="eyebrow" data-en="Oil journal">Appunti d'olio</span>
    <h1 style="font-size:clamp(34px,4.4vw,56px);font-weight:400;margin-top:12px"><?php echo esc_html( get_the_title() ); ?></h1>
    <p style="font-size:18px;color:var(--ink-soft);margin-top:16px;max-width:60ch" data-en="Curiosities, tips and the charm hidden in every drop of Puglia's oil.">Curiosità, consigli e il fascino che si nasconde in ogni goccia d'olio pugliese.</p>
    <div class="blog-tools">
      <div class="chips">
        <a class="chip<?php echo $lc_cat ? '' : ' active'; ?>" href="<?php echo esc_url( $base ); ?>" data-en="All">Tutti</a>
        <?php
        $cats = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true, 'number' => 8 ) );
        if ( ! is_wp_error( $cats ) ) {
          foreach ( $cats as $cat ) {
            printf( '<a class="chip%s" href="%s">%s</a>',
              $lc_cat === $cat->slug ? ' active' : '',
              esc_url( add_query_arg( 'cat', $cat->slug, $base ) ),
              esc_html( $cat->name )
            );
          }
        }
        ?>
      </div>
      <form class="blog-search" method="get" action="<?php echo esc_url( $base ); ?>">
        <input type="search" name="q" value="<?php echo esc_attr( $lc_q ); ?>" placeholder="Cerca negli appunti…" data-en-ph="Search the journal…" />
        <button type="submit" aria-label="Cerca"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.2-3.2"/></svg></button>
      </form>
    </div>
  </div>
</div>

<section class="block" style="padding-top:56px">
  <div class="wrap">
    <?php if ( $blog->have_posts() ) : $idx = 0; ?>
      <?php while ( $blog->have_posts() ) : $blog->the_post(); $cats = get_the_category(); $catname = $cats ? $cats[0]->name : 'Appunti'; ?>
        <?php if ( 0 === $idx && 1 === $paged && ! $lc_q && ! $lc_cat ) : /* articolo in evidenza */ ?>
          <a class="feat" href="<?php the_permalink(); ?>">
            <div class="feat-media">
              <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'large', array( 'style' => 'position:absolute;inset:0;width:100%;height:100%;object-fit:cover' ) ); } else { echo '<div class="imgslot"></div>'; } ?>
            </div>
            <div class="feat-body">
              <div class="date"><?php echo esc_html__( 'In evidenza', 'lamacupa' ) . ' · ' . esc_html( $catname ); ?></div>
              <h2><?php the_title(); ?></h2>
              <p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 34 ) ); ?></p>
              <span class="add" style="margin-top:22px;font-size:13px;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-deep);font-weight:500" data-en="Read the article →">Leggi l'articolo →</span>
            </div>
          </a>
          <div class="notes" style="margin-top:46px">
        <?php else : ?>
          <?php lamacupa_blog_card( get_the_ID() ); ?>
        <?php endif; ?>
        <?php $idx++; ?>
      <?php endwhile; ?>
      <?php if ( $idx > 1 || ! ( 1 === $paged && ! $lc_q && ! $lc_cat ) ) : ?></div><?php endif; ?>

      <?php
      if ( $blog->max_num_pages > 1 ) {
        echo '<div class="shop-pagination" style="margin-top:48px;text-align:center">';
        echo wp_kses_post( paginate_links( array( 'total' => $blog->max_num_pages, 'current' => $paged, 'base' => add_query_arg( 'paged', '%#%', $base ), 'format' => '' ) ) );
        echo '</div>';
      }
      wp_reset_postdata();
      ?>
    <?php else : ?>
      <p style="color:var(--ink-soft);font-size:17px" data-en="No articles found.">Nessun articolo trovato.</p>
    <?php endif; ?>
  </div>
</section>

<section class="block news">
  <div class="wrap center">
    <span class="eyebrow" data-en="Newsletter">Newsletter</span>
    <h2 data-en="Oil notes, straight to your inbox">Appunti d'olio, dritti nella tua casella</h2>
    <p data-en="We have been taking notes on the world of oil for generations, and we would love to share them with you.">Prendiamo appunti da generazioni sul mondo dell'olio e abbiamo il grande desiderio di condividerli con te.</p>
    <form onsubmit="return false">
      <input type="email" placeholder="La tua email" data-en-ph="Your email" />
      <button class="btn btn-gold" type="submit" data-en="Subscribe">Iscriviti</button>
    </form>
  </div>
</section>

<?php get_footer(); ?>
