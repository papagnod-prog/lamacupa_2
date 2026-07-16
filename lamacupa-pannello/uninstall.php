<?php
// Disinstallazione: rimuove l'opzione salvata dal plugin.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'lamacupa_pannello_config' );
