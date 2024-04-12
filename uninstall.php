<?php

// uninstalling the cron
if ( wp_next_scheduled( 'toptex_cron_event' ) ) {
    wp_clear_scheduled_hook( 'toptex_cron_event' );
}