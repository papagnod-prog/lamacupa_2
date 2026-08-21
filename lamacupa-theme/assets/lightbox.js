/**
 * Lightbox leggero per le gallerie negli articoli del blog.
 *
 * Funziona su due tipi di galleria:
 *  - .legacy-gallery  → gallerie Woodmart tradotte dagli articoli migrati
 *  - .wp-block-gallery / .gallery → il blocco "Galleria" nativo di
 *    WordPress, da usare per i NUOVI articoli (Aggiungi blocco → Galleria,
 *    trascina le foto, pubblica — nessuna shortcode da scrivere a mano).
 *
 * Nessuna dipendenza: al click su una miniatura apre l'immagine a schermo
 * intero, con frecce per scorrere avanti/indietro nella stessa galleria,
 * tasti freccia/Esc da tastiera, e contatore "3 / 12".
 */
(function () {
  'use strict';

  var overlay, imgEl, counterEl, items = [], currentIndex = 0;

  function biggestSrc(img) {
    if (img.dataset.fullUrl) return img.dataset.fullUrl;
    if (img.srcset) {
      var candidates = img.srcset.split(',').map(function (s) {
        var parts = s.trim().split(' ');
        return { url: parts[0], w: parseInt(parts[1], 10) || 0 };
      });
      candidates.sort(function (a, b) { return b.w - a.w; });
      if (candidates[0] && candidates[0].url) return candidates[0].url;
    }
    return img.src;
  }

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
    var it = items[currentIndex];
    imgEl.src = it.full;
    imgEl.alt = it.alt || '';
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
    var groups = [];

    // Gallerie Woodmart tradotte (articoli migrati).
    document.querySelectorAll('.legacy-gallery').forEach(function (gallery) {
      var links = Array.prototype.slice.call(gallery.querySelectorAll('a.legacy-lightbox-item'));
      if (!links.length) return;
      groups.push(links.map(function (a) {
        var img = a.querySelector('img');
        return { el: a, full: a.getAttribute('href'), alt: img ? img.alt : '' };
      }));
    });

    // Blocco "Galleria" nativo di WordPress (nuovi articoli).
    document.querySelectorAll('.wp-block-gallery, .gallery').forEach(function (gallery) {
      var imgs = Array.prototype.slice.call(gallery.querySelectorAll('img'));
      if (!imgs.length) return;
      groups.push(imgs.map(function (img) {
        var link = img.closest('a');
        return { el: link || img, full: link ? link.getAttribute('href') : biggestSrc(img), alt: img.alt };
      }));
    });

    groups.forEach(function (group) {
      group.forEach(function (item, i) {
        item.el.addEventListener('click', function (e) {
          e.preventDefault();
          open(group, i);
        });
      });
    });
  });
})();

