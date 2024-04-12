<?php

// initializing options useful for cron
if ( !get_option('toptex_cron_list') ) {
    add_option( 'toptex_cron_list', [] );
}

if ( !get_option('toptex_products_sku_list') ) {
    add_option( 'toptex_products_sku_list', [] );
}

if ( !get_option('toptex_sku_next_to_update') ) {
    add_option( 'toptex_sku_next_to_update', '' );
}