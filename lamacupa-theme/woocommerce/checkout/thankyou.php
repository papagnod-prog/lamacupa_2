<?php
/**
 * Pagina di conclusione acquisto (Order received / Grazie) — tema Lamacupa.
 *
 * Override del template WooCommerce checkout/thankyou.php.
 * Mantiene gli hook standard così restano compatibili plugin e gateway.
 *
 * @package Lamacupa
 */

defined( 'ABSPATH' ) || exit;

/** @var WC_Order $order */
?>

<section class="thankyou block">
  <div class="wrap">

    <?php if ( $order ) : ?>

      <?php if ( $order->has_status( 'failed' ) ) : ?>

        <div class="ty-head ty-failed">
          <span class="eyebrow" data-en="Payment">Pagamento</span>
          <h1 data-en="Payment not completed">Pagamento non riuscito</h1>
          <p class="ty-lead" data-en="Unfortunately your order cannot be processed as the payment could not be taken. Please try again.">Purtroppo il tuo ordine non può essere elaborato: il pagamento non è andato a buon fine. Riprova pure.</p>
        </div>

        <p class="ty-actions">
          <a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="btn btn-gold" data-en="Pay again">Riprova il pagamento</a>
          <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-ghost" data-en="My account">Il mio account</a>
          <?php endif; ?>
        </p>

      <?php else : ?>

        <div class="ty-head">
          <span class="ty-check" aria-hidden="true">
            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M20 6 9 17l-5-5"/></svg>
          </span>
          <span class="eyebrow" data-en="Thank you">Grazie</span>
          <h1 data-en="Order confirmed">Ordine confermato</h1>
          <p class="ty-lead" data-en="Thank you. We have received your order and we are preparing it with care. You will receive a confirmation email shortly.">Grazie. Abbiamo ricevuto il tuo ordine e lo stiamo preparando con cura. Riceverai a breve un'email di conferma.</p>
        </div>

        <ul class="ty-meta">
          <li>
            <span class="k" data-en="Order number">Numero ordine</span>
            <strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
          </li>
          <li>
            <span class="k" data-en="Date">Data</span>
            <strong><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
          </li>
          <?php if ( $order->get_billing_email() ) : ?>
          <li>
            <span class="k" data-en="Email">Email</span>
            <strong><?php echo esc_html( $order->get_billing_email() ); ?></strong>
          </li>
          <?php endif; ?>
          <li>
            <span class="k" data-en="Total">Totale</span>
            <strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
          </li>
          <li>
            <span class="k" data-en="Payment">Pagamento</span>
            <strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
          </li>
        </ul>

        <?php do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>

        <div class="ty-details">
          <?php
          // Riepilogo prodotti + indirizzi + spedizione (totali calcolati da WooCommerce).
          do_action( 'woocommerce_order_details_after_order_table', $order );
          if ( function_exists( 'woocommerce_order_details_table' ) ) {
            woocommerce_order_details_table( $order->get_id() );
          }
          ?>
        </div>

        <p class="ty-actions">
          <a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="btn btn-gold" data-en="Continue shopping">Continua lo shopping</a>
          <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="btn btn-ghost" data-en="My orders">I miei ordini</a>
          <?php endif; ?>
        </p>

      <?php endif; ?>

    <?php else : ?>

      <div class="ty-head">
        <span class="eyebrow" data-en="Thank you">Grazie</span>
        <h1 data-en="Thank you">Grazie</h1>
        <p class="ty-lead" data-en="Thank you. Your order has been received.">Grazie. Il tuo ordine è stato ricevuto.</p>
      </div>

    <?php endif; ?>

  </div>
</section>
