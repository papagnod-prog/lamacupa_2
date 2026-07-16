#!/bin/sh
# Azione di distribuzione per Plesk (Git → "Attiva azioni di distribuzione aggiuntive")
# Copia tema e plugin nelle cartelle di WordPress dopo ogni push.

# PERCORSO ASSOLUTO DEL TUO DOMINIO SU PLESK
DOCROOT="/var/www/vhosts/dazzling-meitner.136-144-244-35.plesk.page/httpdocs"

# Copia in modo incrementale ed elimina i file rimossi in locale
rsync -a --delete lamacupa-theme/    "$DOCROOT/wp-content/themes/lamacupa-theme/"
rsync -a --delete lamacupa-pannello/ "$DOCROOT/wp-content/plugins/lamacupa-pannello/"