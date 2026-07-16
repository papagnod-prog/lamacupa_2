<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Lamacupa — Pannello</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500;600&family=Manrope:wght@400;500;600;700&family=Cormorant+Garamond:wght@400;500;600&family=EB+Garamond:wght@400;500;600&family=Spectral:wght@300;400;500;600&display=swap" rel="stylesheet" />
<style>

:root{
  --cream:#f8f3e7; --paper:#fcf9f0; --panel:#ffffff;
  --olive:#34401f; --olive-deep:#27300f; --olive-soft:#5c6b3c;
  --gold:#b88a3e; --gold-deep:#9a6f2c;
  --ink:#2b2a20; --ink-soft:#6a6757; --ink-faint:#9c9784;
  --line:rgba(52,64,31,.14); --line-2:rgba(52,64,31,.08);
  --sidebar:#2b3318; --sidebar-2:#222a12;
  --ok:#4f7a35; --warn:#b8703e;
  --r:10px; --shadow:0 1px 2px rgba(40,48,18,.04),0 10px 30px -18px rgba(40,48,18,.3);
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{height:100%}
body{font-family:'Jost',system-ui,sans-serif;background:var(--cream);color:var(--ink);
  font-weight:400;line-height:1.55;-webkit-font-smoothing:antialiased;overflow:hidden}
button{font-family:inherit;cursor:pointer}
input,select,textarea{font-family:inherit}
.mono{font-variant-numeric:tabular-nums}

/* ============ LOGIN ============ */
.login{position:fixed;inset:0;z-index:200;display:grid;place-items:center;
  background:radial-gradient(120% 120% at 70% 0%,#3a4622,#222a12 70%);padding:24px}
.login::before{content:'';position:absolute;inset:0;opacity:.5;
  background:radial-gradient(60% 50% at 20% 90%,rgba(184,138,62,.18),transparent 70%)}
.login-card{position:relative;width:min(410px,100%);background:rgba(248,243,231,.98);
  border-radius:16px;padding:46px 42px 38px;box-shadow:0 40px 90px -30px rgba(0,0,0,.6)}
.login .lk{font-size:11px;letter-spacing:.34em;text-transform:uppercase;color:var(--gold-deep);text-align:center}
.login .lm{font-size:30px;font-weight:600;letter-spacing:.2em;color:var(--olive);text-align:center;margin:8px 0 2px}
.login .ls{font-size:13px;color:var(--ink-soft);text-align:center;letter-spacing:.04em}
.login .frow{margin-top:22px}
.login label{display:block;font-size:11px;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-deep);margin-bottom:7px}
.login input{width:100%;padding:13px 15px;border:1px solid var(--line);background:#fff;border-radius:8px;
  font-size:15px;color:var(--ink)}
.login input:focus{outline:none;border-color:var(--gold)}
.login .enter{width:100%;margin-top:26px;border:none;background:var(--olive);color:var(--cream);
  padding:15px;border-radius:8px;font-size:13px;letter-spacing:.18em;text-transform:uppercase;font-weight:500;transition:.2s}
.login .enter:hover{background:var(--olive-deep)}
.login .hint{margin-top:18px;text-align:center;font-size:12px;color:var(--ink-faint);letter-spacing:.02em}

/* ============ APP SHELL ============ */
.app{display:none;height:100%;grid-template-columns:248px 1fr;grid-template-rows:64px 1fr}
.app.on{display:grid}

/* topbar */
.top{grid-column:1/3;display:flex;align-items:center;justify-content:space-between;
  padding:0 24px;background:var(--panel);border-bottom:1px solid var(--line);z-index:20}
.top .brand{display:flex;align-items:baseline;gap:10px}
.top .brand b{font-size:19px;font-weight:600;letter-spacing:.18em;color:var(--olive)}
.top .brand span{font-size:10px;letter-spacing:.26em;text-transform:uppercase;color:var(--gold-deep)}
.top-right{display:flex;align-items:center;gap:10px}
@media(max-width:1240px){ .pill{display:none} }
@media(max-width:1180px){ .top{padding:0 16px} .brand span{display:none} .tbtn{padding:9px 12px} }
.pill{display:flex;align-items:center;gap:8px;font-size:12px;letter-spacing:.04em;color:var(--ink-soft);
  background:var(--cream);border:1px solid var(--line);padding:7px 13px;border-radius:99px;white-space:nowrap}
.pill .dot{width:7px;height:7px;border-radius:50%;background:var(--ok)}
.tbtn{display:inline-flex;align-items:center;gap:7px;font-size:12.5px;letter-spacing:.06em;white-space:nowrap;
  border:1px solid var(--line);background:#fff;color:var(--olive);padding:9px 14px;border-radius:8px;transition:.18s}
.tbtn:hover{border-color:var(--gold);color:var(--gold-deep)}
.tbtn.solid{background:var(--olive);color:var(--cream);border-color:var(--olive)}
.tbtn.solid:hover{background:var(--olive-deep);color:var(--cream)}
.tbtn svg{width:15px;height:15px}
.pv-toggle{display:none}

/* sidebar */
.side{grid-row:2/3;background:linear-gradient(180deg,var(--sidebar),var(--sidebar-2));
  padding:18px 14px;display:flex;flex-direction:column;gap:3px;overflow-y:auto}
.side .grp{font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:rgba(248,243,231,.4);
  padding:16px 14px 8px}
.nav-i{display:flex;align-items:center;gap:12px;padding:11px 14px;border-radius:8px;
  color:rgba(248,243,231,.78);font-size:14.5px;letter-spacing:.01em;transition:.16s;border:none;background:none;width:100%;text-align:left}
.nav-i svg{width:18px;height:18px;opacity:.85;flex:none}
.nav-i:hover{background:rgba(248,243,231,.07);color:#fff}
.nav-i.active{background:rgba(184,138,62,.2);color:#fff}
.nav-i.active svg{color:var(--gold);opacity:1}
.side .spacer{flex:1}
.side .user{display:flex;align-items:center;gap:11px;padding:12px 12px;border-top:1px solid rgba(248,243,231,.1);margin-top:8px}
.side .ava{width:34px;height:34px;border-radius:50%;background:var(--gold);color:#fff;display:grid;place-items:center;font-weight:600;font-size:14px}
.side .user .nm{font-size:13.5px;color:#fff;line-height:1.2}
.side .user .rl{font-size:11px;color:rgba(248,243,231,.5)}
.side .logout{margin-left:auto;background:none;border:none;color:rgba(248,243,231,.55);display:grid;place-items:center;padding:6px;border-radius:6px}
.side .logout:hover{color:#fff;background:rgba(248,243,231,.08)}

/* main */
.main{grid-row:2/3;display:grid;grid-template-columns:1fr minmax(420px,46%);overflow:hidden}
.editor{overflow-y:auto;padding:30px 34px 80px}
.editor::-webkit-scrollbar{width:10px}
.editor::-webkit-scrollbar-thumb{background:var(--line);border-radius:8px;border:3px solid var(--cream)}

.phead{margin-bottom:24px}
.phead .ek{font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:var(--gold-deep)}
.phead h1{font-size:27px;font-weight:500;color:var(--olive);margin-top:6px;letter-spacing:-.01em}
.phead p{font-size:14px;color:var(--ink-soft);margin-top:6px;max-width:60ch}

.card{background:var(--panel);border:1px solid var(--line);border-radius:var(--r);
  box-shadow:var(--shadow);padding:24px 24px;margin-bottom:18px}
.card h3{font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:var(--gold-deep);margin-bottom:4px}
.card .cdesc{font-size:13px;color:var(--ink-soft);margin-bottom:18px}

.field{margin-bottom:16px}
.field:last-child{margin-bottom:0}
.field label{display:block;font-size:12px;letter-spacing:.04em;color:var(--ink-soft);margin-bottom:6px;font-weight:500}
.field .help{font-size:11.5px;color:var(--ink-faint);margin-top:5px}
.in{width:100%;padding:11px 13px;border:1px solid var(--line);background:var(--paper);border-radius:8px;
  font-size:14px;color:var(--ink);transition:.16s}
.in:focus{outline:none;border-color:var(--gold);background:#fff}
textarea.in{resize:vertical;min-height:74px;line-height:1.5}
.row{display:flex;gap:12px}
.row>*{flex:1}

/* controlli stile per-testo (Pagine) */
.style-ctrls{display:flex;align-items:center;gap:14px;flex-wrap:wrap;margin-top:10px;padding-top:12px;border-top:1px dashed var(--line)}
.style-ctrls .sc{display:flex;align-items:center;gap:7px;font-size:12.5px;color:var(--ink-soft);cursor:pointer;user-select:none}
.style-ctrls .sc input{accent-color:var(--olive)}
.style-ctrls input[type=color]{width:42px;height:34px;border:1px solid var(--line);border-radius:7px;background:none;cursor:pointer;padding:2px}
.style-ctrls input[type=color]:disabled{opacity:.4;cursor:not-allowed}
.style-ctrls .styl-font{width:auto;min-width:170px;padding:8px 11px}

/* color editor */
.cgrid{display:grid;grid-template-columns:repeat(3,1fr);gap:14px}
.swatch{border:1px solid var(--line);border-radius:10px;overflow:hidden;background:var(--paper)}
.swatch .sw{height:74px;position:relative;cursor:pointer}
.swatch .sw input[type=color]{position:absolute;inset:0;width:100%;height:100%;opacity:0;cursor:pointer;border:none}
.swatch .meta{padding:10px 12px}
.swatch .meta .nm{font-size:12px;color:var(--ink);font-weight:500}
.swatch .meta input[type=text]{width:100%;border:none;background:none;font-size:12px;color:var(--ink-soft);
  letter-spacing:.04em;margin-top:2px;text-transform:uppercase}
.swatch .meta input:focus{outline:none;color:var(--gold-deep)}

.presets{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px}
.preset{display:flex;align-items:center;gap:9px;border:1px solid var(--line);background:var(--paper);
  border-radius:99px;padding:7px 14px 7px 9px;font-size:13px;color:var(--ink);transition:.16s}
.preset:hover{border-color:var(--gold)}
.preset .dots{display:flex}
.preset .dots i{width:14px;height:14px;border-radius:50%;margin-left:-5px;border:1.5px solid #fff}
.preset .dots i:first-child{margin-left:0}

/* font chooser */
.fonts{display:flex;flex-direction:column;gap:9px}
.fopt{display:flex;align-items:center;justify-content:space-between;gap:14px;border:1px solid var(--line);
  background:var(--paper);border-radius:9px;padding:13px 16px;transition:.16s;text-align:left}
.fopt:hover{border-color:var(--gold)}
.fopt.sel{border-color:var(--olive);background:#fff;box-shadow:inset 0 0 0 1px var(--olive)}
.fopt .fn{font-size:13px;color:var(--ink-soft);letter-spacing:.02em}
.fopt .fp{font-size:21px;color:var(--olive)}
.fopt .ck{width:18px;height:18px;border-radius:50%;border:1.5px solid var(--line);display:grid;place-items:center;flex:none}
.fopt.sel .ck{border-color:var(--olive);background:var(--olive)}
.fopt.sel .ck::after{content:'';width:7px;height:7px;border-radius:50%;background:#fff}

/* toggle */
.toggle{display:flex;align-items:center;gap:12px}
.sw-tg{width:42px;height:24px;border-radius:99px;background:var(--line);position:relative;transition:.2s;border:none;flex:none}
.sw-tg::after{content:'';position:absolute;top:3px;left:3px;width:18px;height:18px;border-radius:50%;background:#fff;transition:.2s;box-shadow:0 1px 3px rgba(0,0,0,.2)}
.sw-tg.on{background:var(--olive)}
.sw-tg.on::after{left:21px}

/* slides / messages list */
.slide-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px}
.slide-row .grip{color:var(--ink-faint);padding-top:11px;flex:none}
.slide-row .in{flex:1}
.slide-row .del{flex:none;border:1px solid var(--line);background:#fff;color:var(--ink-soft);
  width:38px;height:40px;border-radius:8px;display:grid;place-items:center;transition:.16s}
.slide-row .del:hover{border-color:var(--warn);color:var(--warn)}
.addbtn{display:inline-flex;align-items:center;gap:8px;border:1px dashed var(--line);background:none;
  color:var(--gold-deep);font-size:13px;letter-spacing:.04em;padding:11px 16px;border-radius:8px;width:100%;justify-content:center;transition:.16s}
.addbtn:hover{border-color:var(--gold);background:var(--paper)}

/* slider */
.rng{display:flex;align-items:center;gap:14px}
.rng input[type=range]{flex:1;accent-color:var(--olive)}
.rng .v{font-size:14px;color:var(--olive);font-weight:500;min-width:54px;text-align:right}

/* images grid */
.imgs{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.imgcard{border:1px solid var(--line);border-radius:10px;overflow:hidden;background:var(--paper)}
.imgcard .ph{aspect-ratio:16/10;background:var(--cream-2,#efe7d3) center/cover;position:relative;
  display:grid;place-items:center;color:var(--ink-faint);font-size:12px}
.imgcard .ph img{width:100%;height:100%;object-fit:cover}
.imgcard .lbl{padding:10px 12px 4px;font-size:13px;color:var(--ink);font-weight:500}
.imgcard .sub{padding:0 12px;font-size:11px;color:var(--ink-faint);margin-bottom:8px}
.imgcard .urlin{margin:0 10px 11px;width:calc(100% - 20px);padding:8px 10px;font-size:12px;border:1px solid var(--line);border-radius:7px;background:#fff}
.imgcard .urlin:focus{outline:none;border-color:var(--gold)}

/* products */
.prod{border:1px solid var(--line);border-radius:10px;background:var(--paper);padding:16px;margin-bottom:12px}
.prod .ph{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.prod .ph .n{font-size:13px;color:var(--gold-deep);font-weight:500;letter-spacing:.04em}

/* overview */
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px}
.stat{background:var(--panel);border:1px solid var(--line);border-radius:var(--r);padding:20px;box-shadow:var(--shadow)}
.stat b{display:block;font-size:34px;font-weight:500;color:var(--olive);line-height:1}
.stat span{font-size:12.5px;color:var(--ink-soft);margin-top:6px;display:block;letter-spacing:.02em}
.quick{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.qlink{display:flex;align-items:center;gap:14px;background:var(--panel);border:1px solid var(--line);
  border-radius:var(--r);padding:16px 18px;text-align:left;transition:.16s;box-shadow:var(--shadow)}
.qlink:hover{border-color:var(--gold);transform:translateY(-2px)}
.qlink .ico{width:40px;height:40px;border-radius:9px;background:var(--cream);display:grid;place-items:center;color:var(--gold-deep);flex:none}
.qlink .ico svg{width:20px;height:20px}
.qlink h4{font-size:14.5px;color:var(--olive);font-weight:500}
.qlink p{font-size:12px;color:var(--ink-soft);margin-top:2px}

/* ============ PREVIEW ============ */
.preview{background:#e9e3d2;border-left:1px solid var(--line);display:flex;flex-direction:column;overflow:hidden}
.pv-bar{display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--panel);border-bottom:1px solid var(--line)}
.pv-bar .seg{display:flex;background:var(--cream);border:1px solid var(--line);border-radius:8px;overflow:hidden}
.pv-bar .seg button{border:none;background:none;padding:7px 11px;font-size:12px;color:var(--ink-soft);display:grid;place-items:center}
.pv-bar .seg button.on{background:var(--olive);color:var(--cream)}
.pv-sel{font-size:12.5px;border:1px solid var(--line);background:var(--cream);color:var(--olive);
  padding:7px 10px;border-radius:8px}
.pv-sel:focus{outline:none;border-color:var(--gold)}
.pv-bar .url{flex:1;font-size:12px;color:var(--ink-faint);text-align:center;letter-spacing:.04em;
  background:var(--cream);border:1px solid var(--line);border-radius:8px;padding:7px 12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pv-stage{flex:1;overflow:auto;display:grid;place-items:start center;padding:22px}
.pv-frame{background:#fff;box-shadow:0 18px 50px -22px rgba(40,48,18,.5);border-radius:6px;overflow:hidden;
  transition:width .3s ease;width:100%;height:100%}
.pv-frame.mobile{width:390px;height:780px;border-radius:30px;border:9px solid #1c2110;flex:none}
.pv-frame iframe{width:100%;height:100%;border:none;display:block}
.pv-stage.mobile-stage{place-items:start center;align-content:start}

/* toast */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(20px);z-index:120;
  background:var(--olive-deep);color:var(--cream);font-size:13px;letter-spacing:.04em;padding:12px 22px;border-radius:99px;
  box-shadow:0 12px 30px -10px rgba(0,0,0,.5);opacity:0;pointer-events:none;transition:.3s;display:flex;align-items:center;gap:9px}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.toast .dot{width:7px;height:7px;border-radius:50%;background:var(--gold)}

.page{display:none}
.page.on{display:block}

@media(max-width:1080px){
  .main{grid-template-columns:1fr}
  .preview{display:none}
  .preview.show{display:flex;position:fixed;inset:64px 0 0 248px;z-index:30}
  .pv-toggle{display:inline-flex}
}
</style>
</head>
<body>
<!-- ===================== APP ===================== -->
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

<div class="toast" id="toast"><span class="dot"></span><span id="toastTxt">Salvato</span></div>
<script>window.LMCP_ADMIN = <?php echo wp_json_encode( $lmcp_admin_data ); ?>;</script>
<script src="<?php echo esc_url( LMCP_URL . 'admin/admin.js' ); ?>?v=<?php echo esc_attr( LMCP_VER ); ?>"></script>
</body>
</html>
