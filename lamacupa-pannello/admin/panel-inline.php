<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="lmcp-wrap"><!-- ===================== APP ===================== -->
<div class="app" id="app">
  <!-- TOP -->
  <div class="top">
    <div class="brand"><b>LAMACUPA</b><span>Pannello</span></div>
    <div class="top-right">
      <div class="pill"><span class="dot"></span><span id="statusTxt">Tutte le modifiche salvate</span></div>
      <button class="tbtn pv-toggle" id="pvToggle"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>Anteprima</button>
      <button class="tbtn" id="openSite"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M14 3h7v7M21 3l-9 9M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>Apri il sito</button>
      <button class="tbtn" id="exportBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 15V3M7 8l5-5 5 5M5 21h14"/></svg>Esporta</button>
      <button class="tbtn" id="importBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>Importa</button>
      <button class="tbtn solid" id="resetBtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 12a9 9 0 1 0 3-6.7L3 8M3 3v5h5"/></svg>Ripristina</button>
      <input type="file" id="importFile" accept="application/json" style="display:none" />
    </div>
  </div>

  <!-- SIDE -->
  <nav class="side" id="side">
    <div class="grp">Contenuti</div>
    <button class="nav-i active" data-go="overview"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>Panoramica</button>
    <button class="nav-i" data-go="home"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 10.5 12 3l9 7.5M5 9.5V21h14V9.5"/></svg>Home &amp; Hero</button>
    <button class="nav-i" data-go="pages"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5M9 13h6M9 17h4"/></svg>Pagine</button>
    <button class="nav-i" data-go="banner"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="6" width="18" height="5" rx="1"/><path d="M3 15h12M3 19h8"/></svg>Banner &amp; Slide</button>
    <button class="nav-i" data-go="shop"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 7h12l1 13H5L6 7Z"/><path d="M9 7a3 3 0 0 1 6 0"/></svg>Prodotti</button>
    <button class="nav-i" data-go="images"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="8.5" cy="9.5" r="1.5"/><path d="m4 18 5-5 4 4 3-3 4 4"/></svg>Immagini</button>
    <div class="grp">Aspetto</div>
    <button class="nav-i" data-go="brand"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M12 3l2.5 5 5.5.8-4 3.9 1 5.5L12 16l-5 2.7 1-5.5-4-3.9 5.5-.8L12 3Z"/></svg>Logo &amp; Brand</button>
    <button class="nav-i" data-go="theme"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M12 3a9 9 0 0 0 0 18 3 3 0 0 0 0-6 1.5 1.5 0 0 1 0-3 3 3 0 0 0 0-6Z"/></svg>Colori &amp; Font</button>
    <div class="spacer"></div>
    <div class="user">
      <div class="ava">MR</div>
      <div><div class="nm">Maria Rosa</div><div class="rl">Amministratore</div></div>
      <button class="logout" id="logout" title="Esci"><svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></button>
    </div>
  </nav>

  <!-- MAIN -->
  <div class="main">
    <div class="editor" id="editor"><!-- pages injected --></div>

    <!-- PREVIEW -->
    <div class="preview" id="preview">
      <div class="pv-bar">
        <div class="seg">
          <button id="dvDesk" class="on" title="Desktop"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="2" y="4" width="20" height="13" rx="1"/><path d="M8 21h8M12 17v4"/></svg></button>
          <button id="dvMob" title="Mobile"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="6" y="3" width="12" height="18" rx="2"/><path d="M11 18h2"/></svg></button>
        </div>
        <select class="pv-sel" id="pvPage">
          <option value="index.html">Home</option>
          <option value="storia.html">La Storia</option>
          <option value="olio.html">L'Olio</option>
          <option value="orci.html">Gli Orci</option>
          <option value="shop.html">Shop</option>
          <option value="premi.html">Premi</option>
          <option value="appunti.html">Appunti</option>
          <option value="contatti.html">Contatti</option>
        </select>
        <div class="url" id="pvUrl">lamacupa.it</div>
        <button class="tbtn" id="pvReload" title="Ricarica"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 12a9 9 0 1 0 3-6.7L3 8M3 3v5h5"/></svg></button>
      </div>
      <div class="pv-stage" id="pvStage">
        <div class="pv-frame" id="pvFrame"><iframe id="pvIframe" src="about:blank" title="Anteprima"></iframe></div>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span class="dot"></span><span id="toastTxt">Salvato</span></div></div>
