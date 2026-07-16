#!/bin/sh
# Azione di distribuzione per Plesk (Git → "Attiva azioni di distribuzione aggiuntive")
# Copia tema e plugin nelle cartelle di WordPress dopo ogni push.
# Adatta DOCROOT al percorso reale del sito.
DOCROOT="$HOME/httpdocs"
rsync -a --delete lamacupa-theme/    "$DOCROOT/wp-content/themes/lamacupa-theme/"
rsync -a --delete lamacupa-pannello/ "$DOCROOT/wp-content/plugins/lamacupa-pannello/"
