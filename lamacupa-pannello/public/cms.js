/* ===========================================================
   LAMACUPA CMS — runtime front-end (WordPress)
   Legge la configurazione da window.LamacupaCMSData (iniettata da
   wp_localize_script) e la applica al front-end: palette colori,
   font, banner/slide, hero, immagini e schede prodotto.
   Riceve via postMessage gli aggiornamenti dal pannello admin per
   l'anteprima dal vivo.
   =========================================================== */
(function () {
  'use strict';

  var FONTS = {
    'Jost':               { stack: "'Jost',system-ui,sans-serif",        google: 'Jost:ital,wght@0,300;0,400;0,500;0,600;1,400' },
    'Manrope':            { stack: "'Manrope',system-ui,sans-serif",     google: 'Manrope:wght@300;400;500;600;700' },
    'Cormorant Garamond': { stack: "'Cormorant Garamond',Georgia,serif", google: 'Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400' },
    'EB Garamond':        { stack: "'EB Garamond',Georgia,serif",        google: 'EB+Garamond:ital,wght@0,400;0,500;0,600;1,400' },
    'Spectral':           { stack: "'Spectral',Georgia,serif",           google: 'Spectral:ital,wght@0,300;0,400;0,500;0,600;1,400' }
  };

  function read() { return window.LamacupaCMSData || {}; }

  function styleTag(id) {
    var s = document.getElementById(id);
    if (!s) { s = document.createElement('style'); s.id = id; document.head.appendChild(s); }
    return s;
  }
  function mix(a, pct, b) { return 'color-mix(in srgb, ' + a + ' ' + pct + '%, ' + b + ')'; }

  /* 1. PALETTE + FONT */
  function applyAppearance(cfg) {
    if (!cfg.appearance) return;
    var a = cfg.appearance;
    var cream = a.cream || '#f8f3e7', olive = a.olive || '#34401f', gold = a.gold || '#b88a3e';
    var css = ':root{' +
      '--cream:' + cream + ';' +
      '--cream-2:' + mix(cream, 88, '#2b2a20') + ';' +
      '--paper:' + mix(cream, 78, '#ffffff') + ';' +
      '--olive:' + olive + ';' +
      '--olive-deep:' + mix(olive, 74, '#000000') + ';' +
      '--olive-soft:' + mix(olive, 60, '#c8cf9c') + ';' +
      '--gold:' + gold + ';' +
      '--gold-deep:' + mix(gold, 78, '#000000') + ';' +
      '--line:' + mix(olive, 16, 'transparent') + ';' +
      '}';
    var fontName = a.font && FONTS[a.font] ? a.font : 'Jost';
    var f = FONTS[fontName];
    if (fontName !== 'Jost') {
      var link = document.getElementById('cms-font-link');
      if (!link) { link = document.createElement('link'); link.id = 'cms-font-link'; link.rel = 'stylesheet'; document.head.appendChild(link); }
      var href = 'https://fonts.googleapis.com/css2?family=' + f.google + '&display=swap';
      if (link.getAttribute('href') !== href) link.setAttribute('href', href);
    }
    css += 'body{font-family:' + f.stack + '}';
    styleTag('cms-appearance').textContent = css;
  }

  /* 2. BANNER / SLIDE */
  var bannerTimer = null;
  function applyBanner(cfg) {
    var el = document.querySelector('.announce');
    if (!el) return;
    var b = cfg.banner;
    if (!b) return;
    if (bannerTimer) { clearInterval(bannerTimer); bannerTimer = null; }
    if (b.enabled === false) { el.style.display = 'none'; return; }
    el.style.display = '';
    var msgs = (b.messages || []).filter(function (m) { return (m || '').trim(); });
    if (!msgs.length) { el.style.display = 'none'; return; }
    styleTag('cms-banner-css').textContent =
      '.announce .cms-ann{display:inline-block;transition:opacity .5s ease}' +
      '.announce.cms-fade .cms-ann{opacity:0}';
    el.innerHTML = '<span class="cms-ann">' + msgs[0] + '</span>';
    var span = el.querySelector('.cms-ann');
    if (msgs.length > 1) {
      var i = 0, every = Math.max(2, +b.rotate || 4) * 1000;
      bannerTimer = setInterval(function () {
        el.classList.add('cms-fade');
        setTimeout(function () { i = (i + 1) % msgs.length; span.innerHTML = msgs[i]; el.classList.remove('cms-fade'); }, 500);
      }, every);
    }
  }

  /* 3. HERO — singolo o slideshow a più slide */
  function setRich(el, value) { if (el && value != null && value !== '') el.innerHTML = value; }

  function ensureFont(name) {
    if (!name || !FONTS[name] || name === 'Jost') return;
    var id = 'cms-font-' + name.replace(/\s+/g, '-').toLowerCase();
    if (document.getElementById(id)) return;
    var link = document.createElement('link');
    link.id = id; link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=' + FONTS[name].google + '&display=swap';
    document.head.appendChild(link);
  }

  function heroSlides(h) {
    if (h && Array.isArray(h.slides) && h.slides.length) return h.slides;
    return [{ eyebrow: h && h.eyebrow, title: h && h.title, subtitle: h && h.subtitle, cta1: h && h.cta1, cta2: h && h.cta2, image: '' }];
  }

  function applyHero(cfg) {
    var h = cfg.hero;
    if (!h) return;
    var hero = document.querySelector('.hero');
    if (!hero) return;
    var slides = heroSlides(h);
    var imgs = cfg.images || {};
    var heroFallback = (imgs['a-hero'] || '').trim();

    var origCtas = hero.querySelectorAll('.hero-inner .hero-cta a');
    var href1 = origCtas[0] ? origCtas[0].getAttribute('href') : '#';
    var href2 = origCtas[1] ? origCtas[1].getAttribute('href') : '#';

    /* azzera lo slider eventualmente costruito prima (anteprima live) */
    if (window.__lamacupaHeroTimer) { clearInterval(window.__lamacupaHeroTimer); window.__lamacupaHeroTimer = null; }
    Array.prototype.forEach.call(hero.querySelectorAll('[data-hero-generated]'), function (n) { n.remove(); });
    hero.classList.remove('hero-has-slider');

    /* --- una sola slide: comportamento classico --- */
    if (slides.length <= 1) {
      var s0 = slides[0] || {};
      setRich(hero.querySelector('.hero-inner .eyebrow') || hero.querySelector('.eyebrow'), s0.eyebrow);
      setRich(hero.querySelector('.hero-inner h1') || hero.querySelector('h1'), s0.title);
      setRich(hero.querySelector('.hero-inner p') || hero.querySelector('p'), s0.subtitle);
      if (origCtas[0]) setRich(origCtas[0], s0.cta1);
      if (origCtas[1]) setRich(origCtas[1], s0.cta2);
      var img0 = (s0.image || '').trim() || heroFallback;
      var slot = hero.querySelector('#a-hero');
      if (slot && img0) {
        if (slot.tagName.toLowerCase() === 'image-slot') { slot.setAttribute('src', img0); }
        else { slot.style.backgroundImage = 'url("' + img0.replace(/"/g, '%22') + '")'; slot.classList.add('has-img'); }
      }
      return;
    }

    /* --- più slide: costruisci lo slideshow --- */
    hero.classList.add('hero-has-slider');
    var track = document.createElement('div');
    track.className = 'hero-slides'; track.setAttribute('data-hero-generated', '');
    slides.forEach(function (s, i) {
      var img = (s.image || '').trim() || (i === 0 ? heroFallback : '');
      var bg = img ? ' style="background-image:url(\'' + img.replace(/'/g, '%27') + '\')"' : '';
      var slide = document.createElement('div');
      slide.className = 'hero-slide' + (i === 0 ? ' active' : '');
      slide.innerHTML =
        '<div class="hero-bg"' + bg + '></div>' +
        '<div class="wrap hero-inner">' +
          '<span class="eyebrow">' + (s.eyebrow || '') + '</span>' +
          '<h1>' + (s.title || '') + '</h1>' +
          '<p>' + (s.subtitle || '') + '</p>' +
          '<div class="hero-cta">' +
            (s.cta1 ? '<a class="btn btn-gold" href="' + href1 + '">' + s.cta1 + '</a>' : '') +
            (s.cta2 ? '<a class="btn btn-ghost" href="' + href2 + '">' + s.cta2 + '</a>' : '') +
          '</div>' +
        '</div>';
      track.appendChild(slide);
    });
    hero.appendChild(track);

    var dots = document.createElement('div');
    dots.className = 'hero-dots'; dots.setAttribute('data-hero-generated', '');
    slides.forEach(function (_, i) {
      var d = document.createElement('button');
      d.type = 'button'; d.className = 'hero-dot' + (i === 0 ? ' active' : '');
      d.setAttribute('aria-label', 'Slide ' + (i + 1));
      dots.appendChild(d);
    });
    hero.appendChild(dots);

    var prev = document.createElement('button');
    prev.type = 'button'; prev.className = 'hero-nav prev'; prev.setAttribute('data-hero-generated', '');
    prev.setAttribute('aria-label', 'Precedente');
    prev.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M15 6l-6 6 6 6"/></svg>';
    var next = document.createElement('button');
    next.type = 'button'; next.className = 'hero-nav next'; next.setAttribute('data-hero-generated', '');
    next.setAttribute('aria-label', 'Successivo');
    next.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 6l6 6-6 6"/></svg>';
    hero.appendChild(prev); hero.appendChild(next);

    var slideEls = track.querySelectorAll('.hero-slide');
    var dotEls = dots.querySelectorAll('.hero-dot');
    var cur = 0;
    function show(n) {
      cur = (n + slideEls.length) % slideEls.length;
      slideEls.forEach(function (el, i) { el.classList.toggle('active', i === cur); });
      dotEls.forEach(function (el, i) { el.classList.toggle('active', i === cur); });
    }
    function restart() {
      if (window.__lamacupaHeroTimer) clearInterval(window.__lamacupaHeroTimer);
      window.__lamacupaHeroTimer = setInterval(function () { show(cur + 1); }, Math.max(3, +h.rotate || 6) * 1000);
    }
    dotEls.forEach(function (d, i) { d.addEventListener('click', function () { show(i); restart(); }); });
    prev.addEventListener('click', function () { show(cur - 1); restart(); });
    next.addEventListener('click', function () { show(cur + 1); restart(); });
    restart();
  }

  /* 4. IMMAGINI — image-slot (src) oppure .imgslot (background) */
  function applyImages(cfg) {
    var imgs = cfg.images || {};
    Object.keys(imgs).forEach(function (id) {
      var url = (imgs[id] || '').trim();
      if (!url) return;
      var node = document.getElementById(id);
      if (!node) return;
      if (node.tagName.toLowerCase() === 'image-slot') {
        if (node.getAttribute('src') !== url) node.setAttribute('src', url);
      } else {
        node.style.backgroundImage = 'url("' + url.replace(/"/g, '%22') + '")';
        node.classList.add('has-img');
      }
    });
  }

  /* 5. SHOP */
  function applyShop(cfg) {
    var prods = cfg.shop;
    if (!prods || !prods.length) return;
    var grid = document.querySelector('.cards');
    if (!grid) return;
    var cards = grid.querySelectorAll('.card');
    prods.forEach(function (p, idx) {
      var card = cards[idx];
      if (!card) return;
      if (p.name)  setRich(card.querySelector('.card-body h4'), p.name);
      if (p.cat)   setRich(card.querySelector('.card-body .ct'), p.cat);
      if (p.price) setRich(card.querySelector('.card-foot .p'), p.price);
      if (p.tag != null) {
        var tag = card.querySelector('.card-tag');
        if (p.tag === '') { if (tag) tag.style.display = 'none'; }
        else if (tag) { tag.style.display = ''; setRich(tag, p.tag); }
      }
    });
  }

  /* 6. LOGHI (header / footer) */
  function setLogo(sel, url, h) {
    var m = document.querySelector(sel);
    if (!m) return;
    url = (url || '').trim();
    if (!url) return; /* vuoto: lascia il testo "LAMACUPA" */
    m.innerHTML = '<img src="' + url.replace(/"/g, '%22') + '" alt="Lamacupa" style="height:' + ( h || 34 ) + 'px;width:auto;display:block">';
    m.classList.add('has-logo');
  }
  function applyBrand(cfg) {
    var b = cfg.brand;
    if (!b) return;
    setLogo('.brand .mark', b.logoHeader, b.logoH || 34);
    setLogo('.foot-brand .mark', b.logoFooter, b.logoF || 40);
  }

  /* 7. PAGINE (occhiello, titolo, intro + stile per-testo della pagina corrente) */
  function styleEl(el, st) {
    if (!el || !st) return;
    el.style.color = st.color ? st.color : '';
    if (st.font && FONTS[st.font]) { ensureFont(st.font); el.style.fontFamily = FONTS[st.font].stack; }
    else { el.style.fontFamily = ''; }
  }
  function applyPages(cfg) {
    var pages = cfg.pages;
    if (!pages) return;
    var slug = (document.body && document.body.getAttribute('data-lmcp-page')) || '';
    var pg = pages[slug];
    if (!pg) return;
    var head = document.querySelector('.pagehero, .shopbar');
    var eyeEl = null, titleEl = null;
    if (head) {
      eyeEl = head.querySelector('.eyebrow');
      titleEl = head.querySelector('h1');
      // L'occhiello resta un accento gestito dal pannello.
      setRich(eyeEl, pg.eyebrow);
      // Titolo: se il pannello lo definisce, sostituisce quello di WordPress;
      // se il campo è vuoto resta il titolo della pagina.
      if (titleEl && pg.title && String(pg.title).trim()) {
        setRich(titleEl, pg.title);
      }
    }
    // Testo introduttivo: come sopra, il pannello vince solo se compilato.
    var introOverride = document.querySelector('.pagehero .intro, .pagehero p');
    if (introOverride && pg.intro && String(pg.intro).trim()) {
      setRich(introOverride, pg.intro);
    }
    var introEl = document.querySelector('.prose p.intro') || document.querySelector('.prose p');
    var st = pg.style || {};
    styleEl(eyeEl, st.eyebrow);
    styleEl(titleEl, st.title);
    styleEl(introEl, st.intro);
  }

  function apply() {
    var cfg = read();
    try { applyAppearance(cfg); } catch (e) {}
    try { applyBanner(cfg); }     catch (e) {}
    try { applyHero(cfg); }       catch (e) {}
    try { applyImages(cfg); }     catch (e) {}
    try { applyShop(cfg); }       catch (e) {}
    try { applyBrand(cfg); }      catch (e) {}
    try { applyPages(cfg); }      catch (e) {}
  }

  window.LamacupaCMS = { apply: apply, read: read, FONTS: FONTS };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { apply(); setTimeout(apply, 0); });
  } else { apply(); }

  /* aggiornamenti dal pannello (anteprima dal vivo) */
  window.addEventListener('message', function (e) {
    var d = e && e.data;
    if (d && typeof d === 'object' && d.type === 'lamacupa-pannello:apply') {
      if (d.config) window.LamacupaCMSData = d.config;
      apply();
    } else if (d === 'lamacupa-pannello:apply') {
      apply();
    }
  });
})();
