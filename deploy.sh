#!/bin/sh
# Azione di distribuzione per Plesk (Git → "Attiva azioni di distribuzione aggiuntive")
# Copia tema e plugin nelle cartelle di WordPress dopo ogni push.
#
# Lo script viene eseguito da Plesk con working directory = wp-content
# (il "Percorso server" configurato), quindi usiamo percorsi relativi:
# nessun path assoluto, per evitare problemi di permessi/chroot sul server.

rm -rf themes/lamacupa-theme
mkdir -p themes/lamacupa-theme
cp -a lamacupa-theme/. themes/lamacupa-theme/

rm -rf plugins/lamacupa-pannello
mkdir -p plugins/lamacupa-pannello
cp -a lamacupa-pannello/. plugins/lamacupa-pannello/
