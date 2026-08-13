#!/bin/sh
# Azione di distribuzione per Plesk (Git → "Attiva azioni di distribuzione aggiuntive")
# Copia tema e plugin nelle cartelle di WordPress dopo ogni push.

# PERCORSO ASSOLUTO DEL TUO DOMINIO SU PLESK
DOCROOT="/var/www/vhosts/lamacupa.it/shop.lamacupa.it"

# Copia senza rsync (non disponibile sul server): elimina la cartella di
# destinazione e ricopia da zero il contenuto aggiornato dal repository.
rm -rf "$DOCROOT/wp-content/themes/lamacupa-theme"
mkdir -p "$DOCROOT/wp-content/themes/lamacupa-theme"
cp -a lamacupa-theme/. "$DOCROOT/wp-content/themes/lamacupa-theme/"

rm -rf "$DOCROOT/wp-content/plugins/lamacupa-pannello"
mkdir -p "$DOCROOT/wp-content/plugins/lamacupa-pannello"
cp -a lamacupa-pannello/. "$DOCROOT/wp-content/plugins/lamacupa-pannello/"