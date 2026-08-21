/**
 * Lightbox leggero per le gallerie negli articoli del blog
 * ([woodmart_gallery] tradotto in .legacy-gallery).
 *
 * Nessuna dipendenza: al click su una miniatura apre l'immagine a schermo
 * intero, con frecce per scorrere avanti/indietro nella stessa galleria,
 * tasti freccia/Esc da tastiera, e contatore "3 / 12".
 */
(function () {
  'use strict';

  var overlay, imgEl, counterEl, items = [], currentIndex = 0;

  function buildOverlay() {
    if (overlay) return;

    overlay = document.createElement('div');
    overlay.className = 'lmcp-lightbox';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML =
      '<button type="button" class="lmcp-lightbox-close" aria-label="Chiudi">&times;</button>' +
      '<button type="button" class="lmcp-lightbox-prev" aria-label="Precedente">&#10094;</button>' +
      '<div class="lmcp-lightbox-stage"><img alt=""></div>' +
      '<button type="button" class="lmcp-lightbox-next" aria-label="Successiva">&#10095;</button>' +
      '<div class="lmcp-lightbox-counter"></div>';

    document.body.appendChild(overlay);

    imgEl = overlay.querySelector('img');
    counterEl = overlay.querySelector('.lmcp-lightbox-counter');

    overlay.querySelector('.lmcp-lightbox-close').addEventListener('click', close);
    overlay.querySelector('.lmcp-lightbox-prev').addEventListener('click', function () { show(currentIndex - 1); });
    overlay.querySelector('.lmcp-lightbox-next').addEventListener('click', function () { show(currentIndex + 1); });

    // Click sullo sfondo (fuori dall'immagine) chiude.
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
  }

  function show(index) {
    if (!items.length) return;
    currentIndex = (index + items.length) % items.length;
    var full = items[currentIndex].getAttribute('href');
    var alt = items[currentIndex].querySelector('img') ? items[currentIndex].querySelector('img').alt : '';
    imgEl.src = full;
    imgEl.alt = alt;
    counterEl.textContent = (currentIndex + 1) + ' / ' + items.length;
  }

  function open(group, index) {
    buildOverlay();
    items = group;
    show(index);
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    if (!overlay) return;
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  document.addEventListener('keydown', function (e) {
    if (!overlay || !overlay.classList.contains('is-open')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') show(currentIndex - 1);
    if (e.key === 'ArrowRight') show(currentIndex + 1);
  });

  document.addEventListener('DOMContentLoaded', function () {
    var galleries = document.querySelectorAll('.legacy-gallery');
    galleries.forEach(function (gallery) {
      var links = Array.prototype.slice.call(gallery.querySelectorAll('a.legacy-lightbox-item'));
      links.forEach(function (link, i) {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          open(links, i);
        });
      });
    });
  });
})();
