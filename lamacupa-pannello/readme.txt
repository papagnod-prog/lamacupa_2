=== Lamacupa CMS ===
Contributors: lamacupa
Tags: cms, content, theming, shortcode, olio
Requires at least: 5.6
Tested up to: 6.5
Requires PHP: 7.2
Stable tag: 1.5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Pannello di gestione contenuti per il sito Lamacupa: testi, immagini, colori, font, banner/slide e prodotti, con anteprima dal vivo.

== Description ==

Lamacupa CMS aggiunge in wp-admin un pannello "Lamacupa" dal quale gestire
senza toccare il codice:

* **Home & Hero** — sopra-titolo, titolo, sottotitolo e pulsanti
* **Banner & Slide** — la striscia in alto, con più messaggi a rotazione
* **Prodotti** — nome, categoria, prezzo ed etichetta delle schede in vetrina
* **Immagini** — URL per le aree principali della home
* **Colori & Font** — tre colori base (le tonalità di supporto sono calcolate
  in automatico) e cinque famiglie tipografiche
* **Esporta / Importa / Ripristina** la configurazione in JSON

Le impostazioni vengono salvate nelle opzioni di WordPress e applicate dal vivo
al front-end tramite un piccolo runtime. È incluso lo shortcode
`[lamacupa_home]` che pubblica una home già impaginata e completamente
pilotabile dal pannello.

== Installation ==

1. In wp-admin vai su **Plugin → Aggiungi nuovo → Carica plugin**.
2. Seleziona il file `lamacupa-pannello.zip` e premi **Installa ora**, poi **Attiva**.
3. Crea una pagina (es. "Home"), inserisci lo shortcode `[lamacupa_home]` e
   pubblicala. In **Impostazioni → Lettura** impostala come *home page statica*
   (facoltativo).
4. Apri il menu **Lamacupa** in wp-admin: modifica i contenuti e guarda
   l'anteprima aggiornarsi in tempo reale.

Il pannello può gestire anche un tema esistente che utilizzi le stesse classi
del design system Lamacupa (`.announce`, `.hero`, `.cards .card`, ecc.):
in quel caso le immagini si impostano sugli elementi `<image-slot>` o `.imgslot`
tramite l'id corrispondente (`a-hero`, `a-bottle`, `a-p1`…).

== Frequently Asked Questions ==

= Dove vengono salvati i contenuti? =
Nell'opzione `lamacupa_pannello_config` del database di WordPress (tabella options).

= Le modifiche sono multi-utente? =
Sì: a differenza della versione statica (che salvava nel browser), qui la
configurazione è salvata sul server ed è valida per tutti i visitatori.

== Changelog ==

= 1.8.0 =
* Pannello incluso direttamente in wp-admin (niente più iframe): navigazione
  più fluida e piena compatibilità con gli script di WordPress.
* Libreria Media: ogni campo immagine (slide dell'hero, aree immagine della
  Home, copertine delle pagine) ha ora il pulsante "Libreria" per scegliere
  una foto caricata su WordPress, in alternativa all'URL.
* Pagine: TITOLO e TESTO INTRODUTTIVO tornano modificabili dal pannello; se
  lasciati vuoti la pagina usa il contenuto dell'editor di WordPress.
* Home: l'hero parte con TRE slide predefinite (Coratina, Orci, Raccolto).


= 1.5.0 =
* Pagine: il corpo (testi e immagini) si modifica ora dall'editor nativo di
  WordPress; il pannello regola occhiello, copertina e stile (colore/font).
  Il runtime non sovrascrive più titolo e testo della pagina.

= 1.4.0 =
* Home & Hero ora gestisce uno SLIDESHOW a più slide (immagine, testi, pulsanti
  per ogni slide) con velocità di rotazione regolabile; frecce e puntini
  compaiono automaticamente con due o più slide.
* Sezione "Pagine": per occhiello, titolo e intro di ogni pagina si possono
  ora impostare COLORE e FONT indipendenti (vuoto = eredita dal tema).
* Compatibilità con il tema aggiornato (menu da Aspetto → Menu, drawer carrello
  e shop WooCommerce).

= 1.2.0 =
* Nuova sezione “Pagine”: modifica occhiello, titolo, intro e copertina di
  ogni pagina (Storia, Olio, Orci, Shop, Premi, Appunti, Contatti) con
  anteprima che si sposta sulla pagina scelta.
* Nuova sezione “Logo & Brand”: logo per header e footer (URL) con altezza
  regolabile.

= 1.1.0 =
* Rinominati cartella, costanti (LMCP_) e funzioni (lmcp_) con prefisso unico
  per evitare collisioni con un precedente plugin "lamacupa-cms" e con la
  costante LMC_DIR definita dal tema. Il plugin ora si installa nella cartella
  /plugins/lamacupa-pannello/ e non tocca più la vecchia cartella.

= 1.0.1 =
* Fix: file principale reso autosufficiente (rimosso il require esterno che
  poteva causare un errore fatale in attivazione su alcuni hosting).
* Hardening: definizioni protette da function_exists, include con file_exists,
  entità HTML al posto dei caratteri speciali nei default.

= 1.0.0 =
* Prima versione: pannello admin, salvataggio su opzioni, runtime front-end,
  shortcode [lamacupa_home], esporta/importa/ripristina.
