/* ===========================================================
   LAMACUPA — comportamenti condivisi
   =========================================================== */
(function(){
  'use strict';
  var LANG_KEY = 'lamacupa-lang';

  /* ---------- i18n: swap testo IT/EN ----------
     Ogni elemento traducibile ha un attributo data-en="..." con la versione
     inglese. Al primo caricamento salviamo l'italiano in data-it. */
  function applyLang(lang){
    document.documentElement.lang = lang;
    document.querySelectorAll('[data-en]').forEach(function(el){
      if(!el.hasAttribute('data-it')) el.setAttribute('data-it', el.innerHTML);
      el.innerHTML = el.getAttribute(lang === 'en' ? 'data-en' : 'data-it');
    });
    document.querySelectorAll('[data-en-ph]').forEach(function(el){
      if(!el.hasAttribute('data-it-ph')) el.setAttribute('data-it-ph', el.getAttribute('placeholder') || '');
      el.setAttribute('placeholder', el.getAttribute(lang === 'en' ? 'data-en-ph' : 'data-it-ph'));
    });
    document.querySelectorAll('.lang').forEach(function(b){
      b.innerHTML = lang === 'en'
        ? 'IT / <b>EN</b>'
        : '<b>IT</b> / EN';
    });
    try{ localStorage.setItem(LANG_KEY, lang); }catch(e){}
  }
  function currentLang(){
    try{ return localStorage.getItem(LANG_KEY) || 'it'; }catch(e){ return 'it'; }
  }
  function toggleLang(){ applyLang(currentLang() === 'en' ? 'it' : 'en'); }

  /* ---------- format selector + prezzo ---------- */
  function initFormats(){
    document.querySelectorAll('[data-formats]').forEach(function(group){
      var priceEl = document.querySelector(group.getAttribute('data-price-target'));
      group.querySelectorAll('.fmt').forEach(function(f){
        f.addEventListener('click', function(){
          group.querySelectorAll('.fmt').forEach(function(x){x.classList.remove('active');});
          f.classList.add('active');
          if(priceEl && f.dataset.price){
            priceEl.innerHTML = f.dataset.price + ' <small>/ ' + (f.dataset.size||'') + '</small>';
          }
        });
      });
    });
  }

  /* ---------- carrello (solo visuale) ---------- */
  function initCart(){
    var count = 0;
    document.querySelectorAll('[data-add-cart]').forEach(function(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        count++;
        document.querySelectorAll('.cart-count').forEach(function(c){ c.textContent = count; });
        var lbl = btn.querySelector('[data-add-label]') || btn;
        var ok = currentLang()==='en' ? 'Added ✓' : 'Aggiunto ✓';
        var prev = lbl.textContent;
        lbl.textContent = ok;
        setTimeout(function(){ lbl.textContent = prev; }, 1300);
      });
    });
  }

  /* ---------- menu mobile (drawer laterale) ---------- */
  function initMenu(){
    var burger = document.querySelector('.burger');
    var menu = document.querySelector('.menu');
    if(!burger || !menu) return;
    if(document.querySelector('.m-drawer')) return;

    var style = document.createElement('style');
    style.id = 'm-drawer-css';
    style.textContent = ".m-overlay{position:fixed;inset:0;background:rgba(26,30,12,.5);backdrop-filter:blur(3px);-webkit-backdrop-filter:blur(3px);opacity:0;visibility:hidden;transition:opacity .35s ease,visibility .35s;z-index:60}.m-overlay.open{opacity:1;visibility:visible}.m-drawer{position:fixed;top:0;right:0;height:100%;width:min(86vw,360px);background:var(--cream);border-left:1px solid var(--line);box-shadow:-30px 0 70px rgba(26,30,12,.22);transform:translateX(101%);transition:transform .44s cubic-bezier(.22,.61,.36,1);z-index:61;display:flex;flex-direction:column;padding:24px 30px 32px}.m-drawer.open{transform:translateX(0)}.m-head{display:flex;align-items:center;justify-content:space-between;padding-bottom:22px;border-bottom:1px solid var(--line)}.m-mark{font-size:21px;font-weight:600;letter-spacing:.18em;color:var(--olive)}.m-close{background:none;border:none;color:var(--olive);cursor:pointer;display:grid;place-items:center;padding:2px;margin-right:-4px}.m-nav{flex:1;overflow-y:auto;margin-top:6px}.m-nav ul{list-style:none}.m-nav li{border-bottom:1px solid var(--line)}.m-nav a{display:block;padding:17px 2px;font-size:21px;font-weight:400;letter-spacing:.01em;color:var(--ink);transition:color .2s,padding-left .25s}.m-nav a:hover{color:var(--olive);padding-left:8px}.m-nav a.current{color:var(--gold-deep)}.m-foot{display:flex;align-items:center;justify-content:space-between;gap:14px;padding-top:20px;border-top:1px solid var(--line)}.m-lang{font-size:13px;letter-spacing:.12em}.m-tag{font-size:10px;letter-spacing:.24em;text-transform:uppercase;color:var(--gold-deep)}.m-drawer .m-nav li{opacity:0;transform:translateX(14px)}.m-drawer.open .m-nav li{opacity:1;transform:none;transition:opacity .4s ease,transform .4s ease}.m-drawer.open .m-nav li:nth-child(1){transition-delay:.06s}.m-drawer.open .m-nav li:nth-child(2){transition-delay:.10s}.m-drawer.open .m-nav li:nth-child(3){transition-delay:.14s}.m-drawer.open .m-nav li:nth-child(4){transition-delay:.18s}.m-drawer.open .m-nav li:nth-child(5){transition-delay:.22s}.m-drawer.open .m-nav li:nth-child(6){transition-delay:.26s}.m-drawer.open .m-nav li:nth-child(7){transition-delay:.30s}.m-drawer.open .m-nav li:nth-child(8){transition-delay:.34s}@media(prefers-reduced-motion:reduce){.m-drawer,.m-overlay,.m-drawer .m-nav li{transition:none!important}}";
    document.head.appendChild(style);

    var overlay = document.createElement('div');
    overlay.className = 'm-overlay';

    var drawer = document.createElement('aside');
    drawer.className = 'm-drawer';
    drawer.setAttribute('aria-hidden', 'true');

    var links = '';
    menu.querySelectorAll('a').forEach(function(a){
      var cur = a.classList.contains('active') ? ' class="current"' : '';
      var den = a.getAttribute('data-en');
      var denAttr = den ? ' data-en="' + den.replace(/"/g, '&quot;') + '"' : '';
      links += '<li><a href="' + a.getAttribute('href') + '"' + cur + denAttr + '>' + a.innerHTML + '</a></li>';
    });

    drawer.innerHTML =
      '<div class="m-head">' +
        '<span class="m-mark">LAMACUPA</span>' +
        '<button class="m-close" aria-label="Chiudi"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 6l12 12M18 6 6 18"/></svg></button>' +
      '</div>' +
      '<nav class="m-nav"><ul>' + links + '</ul></nav>' +
      '<div class="m-foot">' +
        '<button class="lang m-lang"><b>IT</b> / EN</button>' +
        '<span class="m-tag">Olio EVO · Corato</span>' +
      '</div>';

    document.body.appendChild(overlay);
    document.body.appendChild(drawer);

    function openMenu(){
      drawer.classList.add('open');
      overlay.classList.add('open');
      drawer.setAttribute('aria-hidden', 'false');
      document.documentElement.style.overflow = 'hidden';
    }
    function closeMenu(){
      drawer.classList.remove('open');
      overlay.classList.remove('open');
      drawer.setAttribute('aria-hidden', 'true');
      document.documentElement.style.overflow = '';
    }

    burger.addEventListener('click', openMenu);
    overlay.addEventListener('click', closeMenu);
    drawer.querySelector('.m-close').addEventListener('click', closeMenu);
    drawer.querySelectorAll('.m-nav a').forEach(function(a){ a.addEventListener('click', closeMenu); });
    drawer.querySelector('.m-lang').addEventListener('click', toggleLang);
    document.addEventListener('keydown', function(e){ if(e.key === 'Escape') closeMenu(); });

    /* allinea la lingua del drawer appena costruito */
    applyLang(currentLang());
  }

  document.addEventListener('DOMContentLoaded', function(){
    applyLang(currentLang());
    document.querySelectorAll('.lang').forEach(function(b){
      b.addEventListener('click', toggleLang);
    });
    initFormats();
    initCart();
    initMenu();
  });
})();
