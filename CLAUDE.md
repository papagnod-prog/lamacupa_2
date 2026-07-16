# Lamacupa — Istruzioni per Claude Code

Sito: **Azienda Agricola Lamacupa** — olio EVO monocultivar Coratina, Puglia.
Stack: WordPress + WooCommerce su server Plesk (PHP/MySQL).
Componenti nostri: tema `lamacupa-theme` + plugin `lamacupa-pannello`.

## Architettura di lavoro — due canali, mai confonderli

1. **CODICE (tema/plugin)** → via Git. Il repo è collegato a Plesk (estensione Git)
   con deploy automatico sul push. NON modificare mai i file PHP direttamente
   sul server via API o wp-admin.
2. **CONTENUTI (pagine, articoli, media, prodotti)** → via REST API con
   l'utente dedicato. NON mettere contenuti hardcoded nel codice del tema.

## Credenziali (mai committarle)

Le credenziali stanno SOLO in variabili d'ambiente o in `.env` (in `.gitignore`):

```
WP_URL=https://www.SITO.it          # sostituire col dominio reale
WP_USER=claude-bot                   # utente WP dedicato (ruolo minimo)
WP_APP_PASSWORD=xxxx xxxx xxxx xxxx  # Utenti → Profilo → Password applicazione
WC_CONSUMER_KEY=ck_...               # WooCommerce → Avanzate → REST API
WC_CONSUMER_SECRET=cs_...
```

Se una chiamata restituisce 401/403: NON riprovare con credenziali inventate,
segnala all'utente che la password applicazione va rigenerata.

## REST API — riferimenti rapidi

Base: `$WP_URL/wp-json/`

```bash
# Autenticazione WordPress (Basic con Application Password)
curl -u "$WP_USER:$WP_APP_PASSWORD" "$WP_URL/wp-json/wp/v2/pages"

# Pagine: leggere / aggiornare (id noti: Storia, Olio, Orci, Shop, Premi, Appunti, Contatti)
curl -u "$WP_USER:$WP_APP_PASSWORD" -X POST \
  "$WP_URL/wp-json/wp/v2/pages/<ID>" \
  -H "Content-Type: application/json" \
  -d '{"content":"<p>...</p>"}'

# Articoli del blog (Appunti)
POST $WP_URL/wp-json/wp/v2/posts        # title, content, status, categories, featured_media

# Media (upload immagine, poi usare l'id come featured_media)
curl -u "$WP_USER:$WP_APP_PASSWORD" -X POST \
  "$WP_URL/wp-json/wp/v2/media" \
  -H "Content-Disposition: attachment; filename=foto.jpg" \
  -H "Content-Type: image/jpeg" --data-binary @foto.jpg

# Prodotti WooCommerce (chiavi WC, non l'app password)
curl -u "$WC_CONSUMER_KEY:$WC_CONSUMER_SECRET" \
  "$WP_URL/wp-json/wc/v3/products"
POST /wp-json/wc/v3/products            # name, sku, regular_price, description,
                                        # categories, images, weight, stock
```

## Convenzioni del progetto

- **Lingua**: tutti i contenuti in italiano.
- **Design system "Premium dorato"** — non deviare: crema `#f8f3e7`, paper
  `#fcf9f0`, oliva `#34401f` / `#27300f`, oro `#b88a3e` / `#9a6f2c`,
  font **Jost**. I token CSS sono in `:root` dentro `style.css`.
- **Versioni**: a ogni modifica del plugin aggiornare `LMCP_VER` (costante nel
  file principale) E l'header `Version:`; per il tema, `LAMACUPA_THEME_VER` in
  `functions.php` e l'header in `style.css`. Serve per il cache-busting.
- **Changelog**: annotare le modifiche del plugin in `readme.txt`.
- **Prezzi**: formato italiano `12,00 €`, valuta EUR.
- **Categorie prodotto**: Olio Luma · Gli Orci · Confezioni regalo.
- **Spedizioni**: Italia gratis oltre 59 €, altrimenti 6,90 € (configurate dal
  setup automatico in `inc/woocommerce-setup.php` — non duplicarle).

## Regole di sicurezza

- Prima di un push che tocca PHP: verifica sintassi (`php -l`) su ogni file
  modificato. Un fatal error va dritto in produzione.
- Non toccare mai il database direttamente; passare sempre dalle API.
- Non modificare la configurazione del pannello (`lamacupa_pannello_config`)
  via API a meno che l'utente non lo chieda esplicitamente: si gestisce dal
  pannello in wp-admin.
- Non cancellare prodotti/pagine/media: al massimo impostare `status: draft`
  e chiedere conferma.
- Operazioni di massa (import, modifiche a più di 5 contenuti): mostrare
  prima all'utente l'elenco di ciò che verrà fatto.

## Struttura del codice

```
lamacupa-theme/
├── functions.php            registrazioni, enqueue, versione tema
├── style.css                design system completo + CSS Woo cart/checkout
├── front-page.php           home (hero slideshow, sezioni)
├── page-*.php               template pagine interne (hero + .prose)
├── single.php               articolo blog
├── assets/site.js           i18n, slideshow, menu mobile
├── assets/cart.js           drawer carrello (fragments AJAX Woo)
├── inc/woocommerce-setup.php  setup automatico shop (idempotente)
└── woocommerce/checkout/thankyou.php  pagina "Ordine ricevuto"

lamacupa-pannello/
├── lamacupa-pannello.php    config, sanitizzazione, AJAX, enqueue
├── admin/panel-inline.php   markup pannello (incluso in wp-admin)
├── admin/admin.js           UI pannello + Libreria Media
├── admin/admin.css          stile pannello (scoped .lmcp-wrap)
├── public/cms.js            runtime front-end (applica config)
└── templates/home.php       home via shortcode [lamacupa_home]
```
