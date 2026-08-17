/* Lamacupa CMS — pannello admin (generato dalla dashboard) */
(function(){
  'use strict';
  var KEY = 'lamacupa-pannello';
  var SES = 'lamacupa-pannello-auth';

  /* ---------- valori di default (= sito attuale) ---------- */
  var DEFAULTS = {
    appearance: { cream:'#f8f3e7', olive:'#34401f', gold:'#b88a3e', font:'Jost' },
    banner: { enabled:true, rotate:4, messages:[
      'Spedizione gratuita in Italia per ordini superiori a <b>€59</b> · Filiera corta · Km Zero'
    ]},
    hero: {
      eyebrow:'Monocultivar Coratina · 100% Italiano',
      title:"L'oro verde<br>di <em>Corato</em>",
      subtitle:'Un olio extra vergine di carattere, estratto a freddo dagli ulivi secolari della nostra terra. Dalla pianta alla tavola, senza intermediari.',
      cta1:'Acquista ora',
      cta2:'La nostra storia',
      rotate:6,
      slides:[
        {
          eyebrow:'Monocultivar Coratina · 100% Italiano',
          title:"L'oro verde<br>di <em>Corato</em>",
          subtitle:'Un olio extra vergine di carattere, estratto a freddo dagli ulivi secolari della nostra terra. Dalla pianta alla tavola, senza intermediari.',
          cta1:'Acquista ora',
          cta2:'La nostra storia',
          image:''
        },
        {
          eyebrow:'Edizione speciale',
          title:"Gli Orci,<br>un <em>filo d'oro</em> di ricordi",
          subtitle:"L'olio custodito come un tempo: orci in terracotta lavorati a mano, un dono che profuma di tradizione.",
          cta1:'Scopri gli Orci',
          cta2:'Idee regalo',
          image:''
        },
        {
          eyebrow:'Raccolto 2025 · Appena franto',
          title:'Dalla raccolta al frantoio<br>in <em>24 ore</em>',
          subtitle:'Spedizione gratuita in Italia oltre €59. Il fruttato intenso della Coratina arriva direttamente a casa tua.',
          cta1:'Vai allo shop',
          cta2:'Come lavoriamo',
          image:''
        }
      ]
    },
    images: {},
    brand: { logoHeader:'', logoFooter:'', logoH:34, logoF:40 },
    pages: {},
    shop: [
      { product_id:0, tag:'' },
      { product_id:0, tag:'' },
      { product_id:0, tag:'' },
      { product_id:0, tag:'' },
      { product_id:0, tag:'' }
    ]
  };

  var FONT_OPTS = ['Jost','Manrope','Cormorant Garamond','EB Garamond','Spectral'];
  var FONT_STACK = {
    'Jost':"'Jost',sans-serif",'Manrope':"'Manrope',sans-serif",
    'Cormorant Garamond':"'Cormorant Garamond',serif",'EB Garamond':"'EB Garamond',serif",
    'Spectral':"'Spectral',serif"
  };
  var PRESETS = [
    { n:'Dorato',  cream:'#f8f3e7', olive:'#34401f', gold:'#b88a3e' },
    { n:'Salvia',  cream:'#eef0e6', olive:'#3f4a32', gold:'#a98b4e' },
    { n:'Notte',   cream:'#f4efe4', olive:'#232a16', gold:'#c79a4a' },
    { n:'Terracotta', cream:'#f7efe3', olive:'#3a3a22', gold:'#bd6b3a' }
  ];
  var IMAGES = [
    { id:'a-hero',   lbl:'Hero — Home',        sub:'Sfondo principale (uliveto / bottiglia)' },
    { id:'a-bottle', lbl:'Bottiglia Luma',     sub:'Sezione prodotto in evidenza' },
    { id:'a-story',  lbl:'La nostra storia',   sub:'Ritratto famiglia / mani al lavoro' },
    { id:'a-coratina', lbl:'Olive Coratina',   sub:'Fascia “il carattere”' },
    { id:'a-orci',   lbl:'Orci in terracotta', sub:'Sezione edizione speciale' }
  ];
  var PAGEDEFS = [
    { slug:'storia', name:'La Storia', hero:'storia-hero' },
    { slug:'olio', name:"L'Olio", hero:'' },
    { slug:'orci', name:'Gli Orci', hero:'orci-hero' },
    { slug:'shop', name:'Shop', hero:'' },
    { slug:'premi', name:'Premi', hero:'premi-hero' },
    { slug:'appunti', name:'Appunti', hero:'' },
    { slug:'contatti', name:'Contatti', hero:'' }
  ];

  /* ---------- stato ---------- */
  function clone(o){ return JSON.parse(JSON.stringify(o)); }
  function load(){
    var stored = {};
    stored = (window.LMCP_ADMIN && LMCP_ADMIN.config) || {};
    var s = clone(DEFAULTS);
    if(stored.appearance) Object.assign(s.appearance, stored.appearance);
    if(stored.banner) Object.assign(s.banner, stored.banner);
    if(stored.hero){ Object.assign(s.hero, stored.hero); if(Array.isArray(stored.hero.slides)) s.hero.slides = clone(stored.hero.slides); }
    if(stored.images) s.images = Object.assign({}, stored.images);
    if(Array.isArray(stored.shop)) s.shop = clone(stored.shop);
    if(stored.brand) Object.assign(s.brand, stored.brand);
    if(stored.pages){ Object.keys(stored.pages).forEach(function(k){ s.pages[k]=Object.assign({}, s.pages[k]||{}, stored.pages[k]); }); }
    return s;
  }
  var state = load();

  function saveRemote(){
    if(!window.LMCP_ADMIN) return;
    var body = 'action=lmcp_save&nonce=' + encodeURIComponent(LMCP_ADMIN.nonce) + '&config=' + encodeURIComponent(JSON.stringify(state));
    fetch(LMCP_ADMIN.ajax, {method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: body})
      .then(function(r){ return r.json(); })
      .then(function(j){ if(!j || !j.success) setStatus('Errore di salvataggio'); })
      .catch(function(){ setStatus('Errore di rete'); });
  }

  var saveT=null;
  function commit(quiet){
    saveRemote();
    applyPreview();
    if(!quiet){
      setStatus('Tutte le modifiche salvate');
      toast('Modifiche salvate');
    }
  }
  /* debounce per gli input di testo */
  function commitSoon(){
    setStatus('Salvataggio…', true);
    clearTimeout(saveT);
    saveT = setTimeout(function(){ commit(true); setStatus('Tutte le modifiche salvate'); }, 350);
  }

  /* ---------- preview ---------- */
  /* nel preview sandbox le pagine richiedono il token presente nell'URL del
     pannello; lo propaghiamo all'iframe. In export/produzione TOKEN è vuoto
     e i percorsi relativi funzionano normalmente. */
  var iframe = document.getElementById('pvIframe');
  function pageUrl(){
    var base = (window.LMCP_ADMIN && LMCP_ADMIN.preview) || '/';
    if(curPage && curPage!=='index.html'){ return base.replace(/\/?$/,'/') + curPage.replace('.html','') + '/'; }
    return base;
  }
  var curPage = 'home';
  function applyPreview(){
    try{ iframe.contentWindow.postMessage({type:'lamacupa-pannello:apply', config: state}, '*'); }catch(e){}
  }
  iframe.addEventListener('load', function(){ setTimeout(applyPreview, 60); });

  document.getElementById('pvPage').addEventListener('change', function(){
    var p = this.value; curPage = p;
    document.getElementById('pvUrl').textContent = 'lamacupa.it/' + (p==='index.html'?'':p.replace('.html',''));
    iframe.src = pageUrl();
  });
  document.getElementById('pvReload').addEventListener('click', function(){ iframe.src = pageUrl(); });
  document.getElementById('openSite').addEventListener('click', function(){ window.open(pageUrl(),'_blank'); });
  var pvT = document.getElementById('pvToggle');
  if(pvT) pvT.addEventListener('click', function(){ document.getElementById('preview').classList.toggle('show'); });

  var frame = document.getElementById('pvFrame'), stage=document.getElementById('pvStage');
  document.getElementById('dvDesk').addEventListener('click', function(){
    frame.classList.remove('mobile'); stage.classList.remove('mobile-stage');
    this.classList.add('on'); document.getElementById('dvMob').classList.remove('on');
  });
  document.getElementById('dvMob').addEventListener('click', function(){
    frame.classList.add('mobile'); stage.classList.add('mobile-stage');
    this.classList.add('on'); document.getElementById('dvDesk').classList.remove('on');
  });

  function previewPage(p){
    var sel=document.getElementById('pvPage'); sel.value=p; 
    sel.dispatchEvent(new Event('change'));
  }

  /* ---------- status + toast ---------- */
  function setStatus(t){ document.getElementById('statusTxt').textContent = t; }
  var toastT=null;
  function toast(t){
    var el=document.getElementById('toast'); document.getElementById('toastTxt').textContent=t;
    el.classList.add('show'); clearTimeout(toastT);
    toastT=setTimeout(function(){ el.classList.remove('show'); },1700);
  }

  /* ---------- helpers UI ---------- */
  function esc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
  function el(html){ var d=document.createElement('div'); d.innerHTML=html.trim(); return d.firstElementChild; }

  /* ---------- Libreria Media di WordPress ---------- */
  var MEDIA_SVG='<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m4 18 5-5 4 4 3-3 4 4"/></svg>';
  function pickMedia(cb){
    if(!(window.wp && wp.media)){ toast('Libreria Media non disponibile'); return; }
    var fr = wp.media({ title:'Scegli un\'immagine', library:{ type:'image' }, multiple:false, button:{ text:'Usa questa immagine' } });
    fr.on('select', function(){
      var a = fr.state().get('selection').first().toJSON();
      var u = (a.sizes && a.sizes.large && a.sizes.large.url) || a.url || '';
      if(u) cb(u);
    });
    fr.open();
  }
  /* Avvolge un input URL con il pulsante "Libreria" che scrive l'URL scelto. */
  function attachMedia(input, onSet){
    var wrap=document.createElement('div'); wrap.className='imgpick';
    input.parentNode.insertBefore(wrap, input); wrap.appendChild(input);
    var b=el('<button type="button" class="mediabtn" title="Scegli dalla Libreria Media">'+MEDIA_SVG+' Libreria</button>');
    b.addEventListener('click', function(ev){ ev.preventDefault();
      pickMedia(function(u){ input.value=u; onSet(u); });
    });
    wrap.appendChild(b);
  }

  /* =========================================================
     RENDER PAGINE
     ========================================================= */
  var editor = document.getElementById('editor');
  var PAGES = {};

  /* ---- OVERVIEW ---- */
  PAGES.overview = function(){
    var p = el('<div class="page" data-p="overview"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Benvenuta, Maria Rosa</div><h1>Panoramica</h1>'
      +'<p>Da qui gestisci testi, immagini, banner, prodotti, colori e font dell\'intero sito. Le modifiche si applicano dal vivo all\'anteprima a destra e a tutte le pagine.</p></div>'
      +'<div class="stats">'
        +'<div class="stat"><b>8</b><span>Pagine pubblicate</span></div>'
        +'<div class="stat"><b class="mono">'+state.shop.length+'</b><span>Prodotti in vetrina</span></div>'
        +'<div class="stat"><b class="mono">'+IMAGES.length+'</b><span>Immagini gestite (Home)</span></div>'
      +'</div>'
      +'<div class="card"><h3>Inizia da qui</h3><div class="cdesc">Le aree modificabili più usate.</div>'
        +'<div class="quick">'
          +qlink('home','Home &amp; Hero','Titolo, sottotitolo e pulsanti')
          +qlink('pages','Pagine','Testi di Storia, Olio, Orci…')
          +qlink('banner','Banner &amp; Slide','Messaggi a scorrimento in alto')
          +qlink('shop','Prodotti','Nomi, categorie e prezzi')
          +qlink('brand','Logo &amp; Brand','Logo header e footer')
          +qlink('theme','Colori &amp; Font','Palette e tipografia del sito')
        +'</div>'
      +'</div>';
    p.querySelectorAll('[data-q]').forEach(function(b){ b.addEventListener('click', function(){ go(b.getAttribute('data-q')); }); });
    return p;
  };
  function qlink(go,t,d){
    return '<button class="qlink" data-q="'+go+'"><span class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>'
      +'<span><h4>'+t+'</h4><p>'+d+'</p></span></button>';
  }

  /* ---- HOME / HERO (slideshow a più slide) ---- */
  PAGES.home = function(){
    var h = state.hero;
    if(!Array.isArray(h.slides) || !h.slides.length){
      h.slides = [{ eyebrow:h.eyebrow||'', title:h.title||'', subtitle:h.subtitle||'', cta1:h.cta1||'', cta2:h.cta2||'', image:(state.images&&state.images['a-hero'])||'' }];
    }
    if(h.rotate==null) h.rotate=6;
    var p = el('<div class="page" data-p="home"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Pagina Home</div><h1>Home &amp; Hero</h1>'
      +'<p>La grande sezione di apertura. Aggiungi più slide per creare uno slideshow rotante. Usa <code>&lt;br&gt;</code> per andare a capo e <code>&lt;em&gt;…&lt;/em&gt;</code> per evidenziare una parola in oro.</p></div>'
      +'<div class="card"><h3>Slide dell\'hero</h3><div class="cdesc">Ogni slide ha immagine, testi e pulsanti. Con due o più slide compaiono rotazione automatica, frecce e puntini.</div>'
        +'<div id="hsList"></div>'
        +'<button class="addbtn" id="hsAdd"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg> Aggiungi slide</button>'
        +'<div class="field" id="hsRotWrap" style="margin-top:22px"><label>Velocità di rotazione</label>'
          +'<div class="rng"><input type="range" id="hsRot" min="3" max="15" step="1" value="'+(h.rotate||6)+'"><span class="v" id="hsRotV">'+(h.rotate||6)+'s</span></div>'
          +'<div class="help">Attiva con due o più slide.</div></div>'
      +'</div>';
    var list = p.querySelector('#hsList');
    function toggleRot(){ p.querySelector('#hsRotWrap').style.display = h.slides.length>1 ? '' : 'none'; }
    function renderList(){
      list.innerHTML='';
      h.slides.forEach(function(sl,i){
        var row = el('<div class="prod">'
          +'<div class="ph"><span class="n">Slide '+(i+1)+'</span>'
            +(h.slides.length>1?'<button class="del" data-del title="Elimina slide" style="margin-left:auto"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg></button>':'')
          +'</div>'
          +'<div class="field"><label>Sopra-titolo (eyebrow)</label><input class="in" data-k="eyebrow" value="'+esc(sl.eyebrow)+'"></div>'
          +'<div class="field"><label>Titolo</label><textarea class="in" data-k="title" rows="2">'+esc(sl.title)+'</textarea></div>'
          +'<div class="field"><label>Sottotitolo</label><textarea class="in" data-k="subtitle" rows="2">'+esc(sl.subtitle)+'</textarea></div>'
          +'<div class="row"><div class="field"><label>Pulsante principale</label><input class="in" data-k="cta1" value="'+esc(sl.cta1)+'"></div>'
          +'<div class="field"><label>Pulsante secondario</label><input class="in" data-k="cta2" value="'+esc(sl.cta2)+'"></div></div>'
          +'<div class="field" style="margin-bottom:0"><label>Immagine di sfondo (URL)</label><input class="in" data-k="image" placeholder="https://…/foto.jpg" value="'+esc(sl.image||'')+'"></div>'
          +'</div>');
        row.querySelectorAll('[data-k]').forEach(function(inp){
          inp.addEventListener('input', function(){ h.slides[i][inp.getAttribute('data-k')]=inp.value; commitSoon(); });
        });
        attachMedia(row.querySelector('[data-k="image"]'), function(u){ h.slides[i].image=u; commitSoon(); });
        var del=row.querySelector('[data-del]');
        if(del) del.addEventListener('click', function(){ h.slides.splice(i,1); renderList(); toggleRot(); commit(); });
        list.appendChild(row);
      });
    }
    renderList(); toggleRot();
    p.querySelector('#hsAdd').addEventListener('click', function(){
      h.slides.push({ eyebrow:'', title:'Nuova slide', subtitle:'', cta1:'Acquista ora', cta2:'Scopri', image:'' });
      renderList(); toggleRot(); commit();
    });
    var rot=p.querySelector('#hsRot');
    rot.addEventListener('input', function(){ h.rotate=+this.value; p.querySelector('#hsRotV').textContent=this.value+'s'; commitSoon(); });
    return p;
  };

  /* ---- BANNER / SLIDE ---- */
  PAGES.banner = function(){
    var b = state.banner;
    var p = el('<div class="page" data-p="banner"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Tutte le pagine</div><h1>Banner &amp; Slide</h1>'
      +'<p>La striscia in cima al sito. Aggiungi più messaggi per farli scorrere a rotazione come slide. Il grassetto si scrive con <code>&lt;b&gt;…&lt;/b&gt;</code>.</p></div>'
      +'<div class="card">'
        +'<div class="toggle" style="margin-bottom:18px"><button class="sw-tg'+(b.enabled?' on':'')+'" id="bnEnable"></button><div><div style="font-size:14px;color:var(--ink);font-weight:500">Mostra il banner</div><div style="font-size:12px;color:var(--ink-soft)">Disattivalo per nascondere la striscia su tutte le pagine</div></div></div>'
        +'<div id="bnBody"></div>'
      +'</div>';
    var body = p.querySelector('#bnBody');
    function renderBody(){
      body.innerHTML =
        '<h3 style="margin-bottom:14px">Slide del banner</h3>'
        +'<div id="bnList"></div>'
        +'<button class="addbtn" id="bnAdd"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 5v14M5 12h14"/></svg> Aggiungi slide</button>'
        +'<div class="field" style="margin-top:22px"><label>Velocità di rotazione</label>'
          +'<div class="rng"><input type="range" id="bnRot" min="2" max="10" step="1" value="'+(b.rotate||4)+'"><span class="v" id="bnRotV">'+(b.rotate||4)+'s</span></div>'
          +'<div class="help">Attiva solo con due o più slide.</div></div>';
      var list = body.querySelector('#bnList');
      b.messages.forEach(function(m,i){
        var row = el('<div class="slide-row"><span class="grip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="9" cy="6" r="1"/><circle cx="9" cy="12" r="1"/><circle cx="9" cy="18" r="1"/><circle cx="15" cy="6" r="1"/><circle cx="15" cy="12" r="1"/><circle cx="15" cy="18" r="1"/></svg></span>'
          +'<input class="in" value="'+esc(m)+'"><button class="del" title="Elimina"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg></button></div>');
        row.querySelector('input').addEventListener('input', function(){ b.messages[i]=this.value; commitSoon(); });
        row.querySelector('.del').addEventListener('click', function(){ b.messages.splice(i,1); if(!b.messages.length) b.messages.push(''); renderBody(); commit(); });
        list.appendChild(row);
      });
      body.querySelector('#bnAdd').addEventListener('click', function(){ b.messages.push('Nuovo messaggio'); renderBody(); commit(); });
      var rot=body.querySelector('#bnRot');
      rot.addEventListener('input', function(){ b.rotate=+this.value; body.querySelector('#bnRotV').textContent=this.value+'s'; commitSoon(); });
    }
    renderBody();
    p.querySelector('#bnEnable').addEventListener('click', function(){ this.classList.toggle('on'); b.enabled=this.classList.contains('on'); commit(); });
    return p;
  };

  /* ---- PRODOTTI ---- */
  PAGES.shop = function(){
    var p = el('<div class="page" data-p="shop"></div>');
    var products = (window.LMCP_ADMIN && LMCP_ADMIN.products) || [];
    p.innerHTML =
      '<div class="phead"><div class="ek">Vetrina &amp; Shop</div><h1>Prodotti</h1>'
      +'<p>Scegli fino a 5 prodotti reali dal catalogo da mostrare in Home. Nome, prezzo e foto sono sempre presi dal prodotto scelto — nessun dato da duplicare.</p></div>'
      +'<div id="prodList"></div>';
    var list=p.querySelector('#prodList');
    if (!products.length) {
      list.innerHTML = '<p style="opacity:.7">Nessun prodotto pubblicato trovato. Pubblica prodotti in WooCommerce per poterli scegliere qui.</p>';
    }
    state.shop.forEach(function(pr,i){
      var opts = '<option value="0">— Nessun prodotto —</option>' + products.map(function(prod){
        return '<option value="'+prod.id+'"'+(String(pr.product_id)===String(prod.id)?' selected':'')+'>'+esc(prod.name)+' ('+esc(prod.price)+')</option>';
      }).join('');
      var row = el('<div class="prod"><div class="ph"><span class="n">Prodotto '+(i+1)+'</span></div>'
        +'<div class="field"><label>Prodotto</label><select class="in" data-k="product_id">'+opts+'</select></div>'
        +'<div class="field"><label>Etichetta (badge)</label><input class="in" data-k="tag" value="'+esc(pr.tag)+'" placeholder="es. Best seller — vuoto = nessuna"></div></div>');
      row.querySelector('[data-k="product_id"]').addEventListener('change', function(){ state.shop[i].product_id = parseInt(this.value,10)||0; commitSoon(); });
      row.querySelector('[data-k="tag"]').addEventListener('input', function(){ state.shop[i].tag = this.value; commitSoon(); });
      list.appendChild(row);
    });
    return p;
  };

  /* ---- IMMAGINI ---- */
  PAGES.images = function(){
    var p = el('<div class="page" data-p="images"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Media della Home</div><h1>Immagini</h1>'
      +'<p>Incolla l\'URL di un\'immagine per ogni area. In alternativa, sul sito puoi trascinare direttamente una foto in ciascuno spazio. Lascia vuoto per usare il segnaposto.</p></div>'
      +'<div class="card"><h3>Aree immagine</h3><div class="cdesc">Le modifiche appaiono nell\'anteprima Home.</div><div class="imgs" id="imgGrid"></div></div>';
    var grid=p.querySelector('#imgGrid');
    IMAGES.forEach(function(im){
      var cur = state.images[im.id]||'';
      var c = el('<div class="imgcard"><div class="ph"></div>'
        +'<div class="lbl">'+im.lbl+'</div><div class="sub">'+im.sub+'</div>'
        +'<input class="urlin" placeholder="https://…/foto.jpg" value="'+esc(cur)+'"></div>');
      var ph=c.querySelector('.ph');
      function paint(u){ ph.innerHTML = u ? '<img src="'+esc(u)+'" alt="">' : 'Nessuna immagine'; }
      paint(cur);
      var urlin=c.querySelector('.urlin');
      urlin.addEventListener('input', function(){
        var v=this.value.trim();
        if(v) state.images[im.id]=v; else delete state.images[im.id];
        paint(v); commitSoon();
      });
      attachMedia(urlin, function(u){ state.images[im.id]=u; paint(u); commitSoon(); });
      grid.appendChild(c);
    });
    return p;
  };

  /* ---- COLORI & FONT ---- */
  PAGES.theme = function(){
    var a = state.appearance;
    var p = el('<div class="page" data-p="theme"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Identità visiva</div><h1>Colori &amp; Font</h1>'
      +'<p>Tre colori guidano l\'intero sito: le tonalità di supporto (sfondi, righe, ombre) vengono calcolate in automatico per restare armoniche.</p></div>'
      +'<div class="card"><h3>Temi pronti</h3><div class="cdesc">Punti di partenza, poi affina i singoli colori.</div>'
        +'<div class="presets" id="presets"></div>'
        +'<div class="cgrid" id="cgrid"></div>'
      +'</div>'
      +'<div class="card"><h3>Tipografia</h3><div class="cdesc">Il font scelto viene applicato a titoli e testi di tutte le pagine.</div>'
        +'<div class="fonts" id="fonts"></div>'
      +'</div>';

    /* presets */
    var pr=p.querySelector('#presets');
    PRESETS.forEach(function(t){
      var b = el('<button class="preset"><span class="dots"><i style="background:'+t.cream+'"></i><i style="background:'+t.olive+'"></i><i style="background:'+t.gold+'"></i></span>'+t.n+'</button>');
      b.addEventListener('click', function(){ a.cream=t.cream; a.olive=t.olive; a.gold=t.gold; renderSwatches(); commit(); });
      pr.appendChild(b);
    });

    /* swatches */
    var cg=p.querySelector('#cgrid');
    var COLS=[['cream','Crema · sfondo'],['olive','Oliva · testo & scuri'],['gold','Oro · accenti']];
    function renderSwatches(){
      cg.innerHTML='';
      COLS.forEach(function(c){
        var key=c[0];
        var s = el('<div class="swatch"><div class="sw" style="background:'+a[key]+'"><input type="color" value="'+a[key]+'"></div>'
          +'<div class="meta"><div class="nm">'+c[1]+'</div><input type="text" value="'+a[key].toUpperCase()+'"></div></div>');
        var sw=s.querySelector('.sw'), pick=s.querySelector('input[type=color]'), hex=s.querySelector('input[type=text]');
        pick.addEventListener('input', function(){ a[key]=this.value; sw.style.background=this.value; hex.value=this.value.toUpperCase(); commitSoon(); });
        hex.addEventListener('change', function(){ var v=this.value.trim(); if(/^#?[0-9a-f]{6}$/i.test(v)){ if(v[0]!=='#')v='#'+v; a[key]=v; sw.style.background=v; pick.value=v; this.value=v.toUpperCase(); commit(); } });
        cg.appendChild(s);
      });
    }
    renderSwatches();

    /* fonts */
    var fb=p.querySelector('#fonts');
    FONT_OPTS.forEach(function(f){
      var o = el('<button class="fopt'+(a.font===f?' sel':'')+'"><span><div class="fn">'+f+'</div><div class="fp" style="font-family:'+FONT_STACK[f]+'">L\'oro verde di Corato</div></span><span class="ck"></span></button>');
      o.addEventListener('click', function(){ a.font=f; fb.querySelectorAll('.fopt').forEach(function(x){x.classList.remove('sel');}); o.classList.add('sel'); commit(); });
      fb.appendChild(o);
    });
    return p;
  };

  /* ---- LOGO & BRAND ---- */
  PAGES.brand = function(){
    var b = state.brand || (state.brand={logoHeader:'',logoFooter:'',logoH:34,logoF:40});
    var p = el('<div class="page" data-p="brand"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Identità</div><h1>Logo &amp; Brand</h1>'
      +'<p>Imposta il logo dell\'azienda tramite URL immagine. Se lasci vuoto resta la scritta “LAMACUPA”. Consigliato PNG o SVG su sfondo trasparente.</p></div>'
      +'<div class="card"><h3>Logo nell\'intestazione</h3><div class="cdesc">In alto, nella barra di navigazione.</div>'
        +'<div class="imgcard" style="max-width:300px;margin-bottom:14px"><div class="ph" id="lhPrev"></div></div>'
        +'<div class="field"><label>URL logo header</label><input class="in" id="lh" placeholder="https://…/logo.png" value="'+esc(b.logoHeader||'')+'"></div>'
        +'<div class="field"><label>Altezza</label><div class="rng"><input type="range" id="lhH" min="20" max="80" step="1" value="'+(b.logoH||34)+'"><span class="v" id="lhHV">'+(b.logoH||34)+'px</span></div></div>'
      +'</div>'
      +'<div class="card"><h3>Logo nel footer</h3><div class="cdesc">In fondo, nella colonna del marchio (sfondo scuro).</div>'
        +'<div class="imgcard" style="max-width:300px;margin-bottom:14px;background:#27300f"><div class="ph" id="lfPrev" style="background:transparent;color:#cbb27a"></div></div>'
        +'<div class="field"><label>URL logo footer</label><input class="in" id="lf" placeholder="https://…/logo-bianco.png" value="'+esc(b.logoFooter||'')+'"></div>'
        +'<div class="field"><label>Altezza</label><div class="rng"><input type="range" id="lfH" min="24" max="90" step="1" value="'+(b.logoF||40)+'"><span class="v" id="lfHV">'+(b.logoF||40)+'px</span></div></div>'
      +'</div>';
    function prev(id,url,h){ var e=p.querySelector(id); e.innerHTML=(url||'').trim()?'<img src="'+esc(url)+'" style="height:'+h+'px;width:auto;object-fit:contain">':'Nessun logo'; }
    prev('#lhPrev', b.logoHeader, b.logoH||34); prev('#lfPrev', b.logoFooter, b.logoF||40);
    p.querySelector('#lh').addEventListener('input', function(){ b.logoHeader=this.value.trim(); prev('#lhPrev',b.logoHeader,b.logoH||34); commitSoon(); });
    p.querySelector('#lf').addEventListener('input', function(){ b.logoFooter=this.value.trim(); prev('#lfPrev',b.logoFooter,b.logoF||40); commitSoon(); });
    p.querySelector('#lhH').addEventListener('input', function(){ b.logoH=+this.value; p.querySelector('#lhHV').textContent=this.value+'px'; prev('#lhPrev',b.logoHeader,b.logoH); commitSoon(); });
    p.querySelector('#lfH').addEventListener('input', function(){ b.logoF=+this.value; p.querySelector('#lfHV').textContent=this.value+'px'; prev('#lfPrev',b.logoFooter,b.logoF); commitSoon(); });
    return p;
  };

  /* ---- PAGINE (testi + stile per-testo: colore & font) ---- */
  PAGES.pages = function(){
    if(!state.pages) state.pages={};
    var p = el('<div class="page" data-p="pages"></div>');
    p.innerHTML =
      '<div class="phead"><div class="ek">Contenuti delle pagine</div><h1>Pagine</h1>'
      +'<p>Qui personalizzi <strong>occhiello, titolo, testo introduttivo, immagine di copertina e stile</strong> di ogni pagina. I campi vuoti ereditano il contenuto della pagina WordPress; il corpo lungo (paragrafi e immagini interne) si modifica dall\'editor nativo (<em>Pagine → Modifica</em>).</p></div>'
      +'<div class="card"><div class="field"><label>Pagina da modificare</label>'
        +'<select class="in" id="pgSel">'+PAGEDEFS.map(function(pd){return '<option value="'+pd.slug+'">'+pd.name+'</option>';}).join('')+'</select></div></div>'
      +'<div id="pgBody"></div>';
    var body=p.querySelector('#pgBody');

    function styleBlock(part){
      return '<div class="style-ctrls">'
        +'<label class="sc"><input type="checkbox" data-on="'+part+'"><span>Colore</span></label>'
        +'<input type="color" data-color="'+part+'" value="#34401f">'
        +'<select class="in styl-font" data-font="'+part+'">'
          +'<option value="">Font predefinito</option>'
          +FONT_OPTS.map(function(f){return '<option value="'+esc(f)+'">'+f+'</option>';}).join('')
        +'</select></div>';
    }

    function renderPage(slug){
      var pd = PAGEDEFS.filter(function(x){return x.slug===slug;})[0];
      var pg = state.pages[slug] || (state.pages[slug]={eyebrow:'',title:'',intro:''});
      if(!pg.style) pg.style={};
      ['eyebrow','title','intro'].forEach(function(pt){ if(!pg.style[pt]) pg.style[pt]={color:'',font:''}; });
      var heroField = pd.hero
        ? '<div class="field"><label>Immagine di copertina (URL)</label><input class="in" id="pgHero" placeholder="https://…/foto.jpg" value="'+esc(state.images[pd.hero]||'')+'"><div class="help">Sfondo del banner di questa pagina.</div></div>'
        : '<div class="help" style="margin-top:4px">Questa pagina non ha un banner immagine dedicato.</div>';
      body.innerHTML =
        '<div class="card"><h3>'+pd.name+'</h3><div class="cdesc">Da qui personalizzi testi e immagine di copertina della pagina. I campi lasciati vuoti usano il contenuto scritto in <em>WordPress → Pagine → '+pd.name+'</em>; il corpo lungo (paragrafi e immagini interne) resta nell\'editor di WordPress.</div>'
        +'<div class="field"><label>Occhiello (eyebrow)</label><input class="in" id="pgEy" value="'+esc(pg.eyebrow||'')+'">'+styleBlock('eyebrow')+'</div>'
        +'<div class="field"><label>Titolo (H1)</label><textarea class="in" id="pgTi" rows="2">'+esc(pg.title||'')+'</textarea><div class="help">Se compilato sostituisce il titolo della pagina. Usa <code>&lt;br&gt;</code> e <code>&lt;em&gt;…&lt;/em&gt;</code> per l\'oro.</div>'+styleBlock('title')+'</div>'
        +'<div class="field"><label>Testo introduttivo</label><textarea class="in" id="pgIn" rows="3">'+esc(pg.intro||'')+'</textarea><div class="help">Se compilato sostituisce il primo paragrafo della pagina.</div>'+styleBlock('intro')+'</div>'
        +heroField
        +'</div>';
      body.querySelector('#pgEy').addEventListener('input', function(){ pg.eyebrow=this.value; commitSoon(); });
      body.querySelector('#pgTi').addEventListener('input', function(){ pg.title=this.value; commitSoon(); });
      body.querySelector('#pgIn').addEventListener('input', function(){ pg.intro=this.value; commitSoon(); });
      ['eyebrow','title','intro'].forEach(function(pt){
        var st  = pg.style[pt];
        var chk = body.querySelector('[data-on="'+pt+'"]');
        var col = body.querySelector('[data-color="'+pt+'"]');
        var fnt = body.querySelector('[data-font="'+pt+'"]');
        if(st.color){ chk.checked=true; col.value=st.color; col.disabled=false; } else { chk.checked=false; col.disabled=true; }
        fnt.value = st.font||'';
        chk.addEventListener('change', function(){ if(this.checked){ col.disabled=false; st.color=col.value; } else { col.disabled=true; st.color=''; } commit(); });
        col.addEventListener('input', function(){ if(chk.checked){ st.color=this.value; commitSoon(); } });
        fnt.addEventListener('change', function(){ st.font=this.value; commit(); });
      });
      if(pd.hero){
        var ph=body.querySelector('#pgHero');
        ph.addEventListener('input', function(){ var v=this.value.trim(); if(v) state.images[pd.hero]=v; else delete state.images[pd.hero]; commitSoon(); });
        attachMedia(ph, function(u){ state.images[pd.hero]=u; commitSoon(); });
      }
      previewPage(slug+'.html');
    }
    p.querySelector('#pgSel').addEventListener('change', function(){ renderPage(this.value); });
    renderPage(PAGEDEFS[0].slug);
    return p;
  };

  /* ---------- navigazione ---------- */
  var current=null;
  function go(name){
    if(!PAGES[name]) name='overview';
    editor.innerHTML='';
    var node=PAGES[name]();
    node.classList.add('on');
    editor.appendChild(node);
    editor.scrollTop=0;
    document.querySelectorAll('.nav-i').forEach(function(b){ b.classList.toggle('active', b.getAttribute('data-go')===name); });
    current=name;
    /* anteprima: pagina unica (home di WordPress) */
  }
  document.querySelectorAll('.nav-i').forEach(function(b){
    b.addEventListener('click', function(){ go(b.getAttribute('data-go')); });
  });

  /* ---------- export / import / reset ---------- */
  document.getElementById('exportBtn').addEventListener('click', function(){
    var blob=new Blob([JSON.stringify(state,null,2)],{type:'application/json'});
    var u=URL.createObjectURL(blob); var a=document.createElement('a');
    a.href=u; a.download='lamacupa-contenuti.json'; a.click(); URL.revokeObjectURL(u);
    toast('Configurazione esportata');
  });
  document.getElementById('importBtn').addEventListener('click', function(){ document.getElementById('importFile').click(); });
  document.getElementById('importFile').addEventListener('change', function(){
    var f=this.files[0]; if(!f) return; var r=new FileReader();
    r.onload=function(){ try{ var j=JSON.parse(r.result); LMCP_ADMIN.config=j; state=load(); commit(); go(current); toast('Configurazione importata'); }catch(e){ toast('File non valido'); } };
    r.readAsText(f); this.value='';
  });
  document.getElementById('resetBtn').addEventListener('click', function(){
    if(!confirm('Ripristinare tutti i contenuti ai valori originali? L\'operazione non è reversibile.')) return;
    LMCP_ADMIN.config=clone(DEFAULTS); state=load(); commit(); go(current); toast('Contenuti ripristinati');
  });

  /* ---------- avvio (in wp-admin l'utente è già autenticato) ---------- */
  function enter(){
    document.getElementById('app').classList.add('on');
    if(iframe.getAttribute('src')==='about:blank') iframe.src = pageUrl();
    go('overview');
    setTimeout(applyPreview, 250);
  }
  enter();
})();
