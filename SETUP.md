# Lamacupa — Setup della pipeline (una volta sola)

## 1. WordPress (15 min)
1. Installa/attiva **WooCommerce** (se manca)
2. **Aspetto → Temi → Aggiungi → Carica**: `lamacupa-theme-v1.5.0.zip` → Attiva
   (all'attivazione lo shop si configura da solo: categorie, 8 prodotti,
   spedizioni 59€/6,90€, bonifico + contrassegno)
3. **Plugin → Aggiungi → Carica**: `lamacupa-pannello-v1.8.0.zip` → Attiva
4. Verifica: home con 3 slide · shop con prodotti · drawer carrello ·
   carrello/checkout a tema · pannello Lamacupa con pulsanti "Libreria"

## 2. Utente API (5 min)
1. **Utenti → Aggiungi**: `claude-bot`, ruolo *Shop Manager* (contenuti + prodotti)
2. Profilo di claude-bot → **Password applicazione** → nuova password → copiala in `.env`
3. **WooCommerce → Impostazioni → Avanzate → REST API** → Aggiungi chiave
   (utente claude-bot, permessi Lettura/Scrittura) → copia ck_/cs_ in `.env`

## 3. Repo GitHub (5 min)
```sh
git init && git add -A && git commit -m "Lamacupa v1.5.0 / pannello v1.8.0"
git branch -M main
git remote add origin git@github.com:TUO-ACCOUNT/lamacupa.git
git push -u origin main
```
(repo **privato**!)

## 4. Plesk (10 min)
1. Pannello Plesk del dominio → **Git** → Aggiungi repository → URL del repo
2. Modalità: distribuzione automatica sul push
3. **Attiva azioni di distribuzione aggiuntive** → comando: `sh deploy.sh`
   (adatta DOCROOT in deploy.sh se il sito non è in httpdocs)
4. Push di prova → controlla che tema e plugin si aggiornino

## 5. Claude Code (10 min)
1. Installa: https://docs.claude.com/en/docs/claude-code/overview
2. `git clone` del repo sul tuo computer, `cp .env.example .env` e compila
3. Apri Claude Code nella cartella e chiedi: *"elencami le pagine del sito"*
   → se risponde con le 7 pagine, il collegamento è vivo ✅

Da qui in poi: le modifiche al CODICE le committi/pushi (deploy automatico),
i CONTENUTI li cambia Claude Code via API direttamente sul sito online.


AtPi6nFZO5dsC5l$KHGPzW4F

QRQr gmsk 9eTI ggcz OyCL r50q

Chiave utente ck_da9ea64e20c6fa15123ed410cd3376aeb011ffcc
Utente nascosto cs_a1d064ec9404509ff6f49246f8e461dbb7fdd225


ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAACAQCxqthNF3JCBku34cfZ+xkOGdtWJiFu8U+SIgu7jIilJ0LRbDutJ98BaJTrc/oFkMkelHe2EnkMrbSN5dxghOmFky1AxSDQJMDQcRfXDRQO0nVrNYMGwWt21z4VOdrUe/Mz18/1Oi4Z5QnP0FkmbXhbqLqdxdAkSoaHkNyC+vO0uHhTYRa+o/0RGCXqK+k3YNN5Euy8YvX5j85wFG2TYxTpKgwbT6WzzwofN1PsG3neryeurP1m2b5T7uSJ/pFqz/A16Hv1+IqmolepJD+K7m96rLDJYsHqrocFPsy1noqcHNYODBEUR6u/aYia34SPTZCiQe5KQmGYXUo7APd0Pmp9gS4Ho810KodygTnxSlgZzQqWFB+cdo7uLQe5/iWBekvVeHYqRMr31m0Gy3U7h+NYokRmQmEwxcvqi1Jk6JUhj6hkrnftrZYgg/V843YTbOC+Gj5qSsejsNGMMEsWohlLYTq22x/Wk/BQtJCvc5sQmdMv9nIZ4fhzPAucG0eaIDd0bBBm76cfcJcJO0vI7RssakKtumy/mzvQDWYPDGBvjtryVNUhZrwRvVkAwHuyAUDoohb/hp2W0G1+Jigm/itRbad9OPCjM9RHaHYv7ASDBNnUydm1Mf4H5cPIl42mdZhG1oeeMmTU3ZgH+arXGTUbBStZZsLbSvdxl3v7skmMgw==