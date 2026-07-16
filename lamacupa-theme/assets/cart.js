/* ===========================================================
   LAMACUPA — drawer carrello laterale
   Si apre al click sull'icona carrello e quando un prodotto viene
   aggiunto (evento WooCommerce "added_to_cart"). I contenuti del
   mini-cart e il contatore sono aggiornati da WooCommerce via
   fragments AJAX. I pulsanti del piè portano a carrello / checkout,
   dove WooCommerce calcola le spese di spedizione.
   =========================================================== */
(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', fn);
    } else { fn(); }
  }

  ready(function () {
    var drawer  = document.querySelector('[data-cart-drawer]');
    var overlay = document.querySelector('[data-cart-overlay]');
    if (!drawer || !overlay) return;

    function open() {
      drawer.classList.add('open');
      overlay.classList.add('open');
      drawer.setAttribute('aria-hidden', 'false');
      document.documentElement.style.overflow = 'hidden';
    }
    function close() {
      drawer.classList.remove('open');
      overlay.classList.remove('open');
      drawer.setAttribute('aria-hidden', 'true');
      document.documentElement.style.overflow = '';
    }

    // Icona carrello nell'header (e qualsiasi [data-open-cart])
    document.querySelectorAll('[data-open-cart]').forEach(function (b) {
      b.addEventListener('click', function (e) { e.preventDefault(); open(); });
    });
    overlay.addEventListener('click', close);
    var closeBtn = drawer.querySelector('[data-cart-close]');
    if (closeBtn) closeBtn.addEventListener('click', close);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });

    // WooCommerce: apri il drawer dopo l'aggiunta AJAX al carrello.
    if (window.jQuery) {
      window.jQuery(document.body).on('added_to_cart', function () { open(); });
      // tasto "rimuovi" dentro il mini-cart: WooCommerce ricarica i fragments
      window.jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {});
    }

    // Espone un'API minima (utile alla demo statica senza WooCommerce).
    window.LamacupaCartDrawer = { open: open, close: close };
  });
})();
