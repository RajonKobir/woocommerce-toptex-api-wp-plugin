<?php
/*
 * Plugin Name: Woocommerce Toptex API
 * Plugin URI: 
 * Description: Woocommerce Toptex API connects 3rd party Toptex API To Woocommerce Store
 * Author: Rajon Kobir
 * Version: 1.0.0
 * Author URI: https://github.com/RajonKobir
 * Text Domain: WoocommerceToptexApi
 * License: GPL2+
 * Domain Path: 
**/


//  no direct access 
if( !defined('ABSPATH') ) : exit(); endif;


// if no woocommerce return from here
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){
    add_action( 'admin_notices', 'woocommerce_toptex_api_admin_warning');
    function woocommerce_toptex_api_admin_warning(){
        echo '<div class="notice notice-warning is-dismissible">
            <p>Please Install & Activate WooCommerce Plugin To Deal With woocommerce_toptex_api Plugin</p>
        </div>';
    }
    return;
}


// Define plugin constants 
define( 'WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH', trailingslashit( plugin_dir_path(__FILE__) ) );
define( 'WOOCOMMERCE_TOPTEX_API_PLUGIN_URL', trailingslashit( plugins_url('/', __FILE__) ) );
define( 'WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME', 'woocommerce_toptex_api' );


// adding settings link into plugin list page
if( is_admin() ) {
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'toptex_settings_link' );
    function toptex_settings_link( array $links ) {
        $settings_url = get_admin_url() . "admin.php?page=woocommerce_toptex_api_settings_page";
        $settings_link = '<a href="' . $settings_url . '" aria-label="' . __('View Toptex Settings', WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME ) . ' ">' . __('Settings', WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME ) . '</a>';
		$action_links = array(
			'settings' => $settings_link,
		);
		return array_merge( $action_links, $links );
    }
}
// adding settings link into plugin list page ends here


// clearing unexpected characters
function toptex_secure_input($data) {
    $data = strval($data);
    $data = strtolower($data);
    $data = trim($data);
    $data = preg_replace('/\s+/', ' ', $data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $special_characters = ['&amp;', '&#38;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&quot;', '&plus;', '&#43;', '&#x2B;', '&#8722;', '&#x2212;', '&minus;', '&ndash;', '&mdash;', '&reg;', '&#174;', '&sol;', '&#47;', '&bsol;', '&#92;', '&copy;', '&#169;', '&equals;', '&#x3D;', '&#61;', '^', '&', '=' ];
    foreach($special_characters as $key => $single_character){
        $data = str_replace($single_character, '&', $data);
    }
    $data = htmlspecialchars_decode($data);
    return $data;
}


// admin or not
if( is_admin() ) {
    // admin settings page
    require_once WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . '/inc/settings/settings.php';
    //  add shortcodes 
    require_once WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . '/inc/shortcodes/shortcodes.php';
}


// register activation hook
register_activation_hook(
	__FILE__,
	'woocommerce_toptex_api_activation_function'
);
function woocommerce_toptex_api_activation_function(){
    require_once WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . 'install.php';
}


// register deactivation hook
register_deactivation_hook(
	__FILE__,
	'woocommerce_toptex_api_deactivation_function'
);
function woocommerce_toptex_api_deactivation_function(){
    require_once WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . 'uninstall.php';
}



// custom image upload
function woocommerce_toptex_api_custom_image_file_upload( $api_image_url, $api_image_name ) {

	// it allows us to use download_url() and wp_handle_sideload() functions
	require_once( ABSPATH . 'wp-admin/includes/file.php' );

	// download to temp dir
	$temp_file = download_url( $api_image_url );

	if( is_wp_error( $temp_file ) ) {
		return false;
	}

    $image_full_name = basename( $temp_file );
    $image_name_array = explode( '.', $image_full_name);
    $image_name = $image_name_array[0];
    $image_extension = $image_name_array[1];

    $updated_image_full_name = $api_image_name . '.' . $image_extension;

	// move the temp file into the uploads directory
	$file = array(
		'name'     => $updated_image_full_name,
		'type'     => mime_content_type( $temp_file ),
		'tmp_name' => $temp_file,
		'size'     => filesize( $temp_file ),
	);
	$sideload = wp_handle_sideload(
		$file,
		array(
            // no needs to check 'action' parameter
			'test_form'   => false 
		)
	);

	if( ! empty( $sideload[ 'error' ] ) ) {
		// you may return error message if you want
		return false;
	}

	// it is time to add our uploaded image into WordPress media library
	$attachment_id = wp_insert_attachment(
		array(
			'guid'           => $sideload[ 'url' ],
			'post_mime_type' => $sideload[ 'type' ],
			'post_title'     => basename( $sideload[ 'file' ] ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		),
		$sideload[ 'file' ]
	);

	if( is_wp_error( $attachment_id ) || ! $attachment_id ) {
		return false;
	}

	// update medatata, regenerate image sizes
	require_once( ABSPATH . 'wp-admin/includes/image.php' );

	wp_update_attachment_metadata(
		$attachment_id,
		wp_generate_attachment_metadata( $attachment_id, $sideload[ 'file' ] )
	);

    @unlink( $temp_file );

	return $attachment_id;

}
// custom image upload ends here



// On Successful WC Checkout
add_action('woocommerce_order_status_completed', 'toptex_order' );
function toptex_order( $order_id ) {
    // Getting an instance of the order object
    $order = wc_get_order( $order_id );
    if($order->is_paid()){
        // initializing
        $ProductLines = [];
        foreach ( $order->get_items() as $item_id => $item ) {
            // if variable product
            if( $item['variation_id'] > 0 ){
                $variation_id = $item['variation_id']; 
                $product_id = $item['product_id'];
                $toptex_cron_list = get_option('toptex_cron_list');
                // if toptex product
                if( count($toptex_cron_list) > 0 ){
                    $keys = array_keys($toptex_cron_list);
                    // checking if toptex product or not
                    if (in_array($product_id, $keys)) {
                        // Get the product object
                        $product = wc_get_product( $variation_id );
                        $variant_sku = $product->sku;
                        $variant_quantity = $item["quantity"];
                        array_push($ProductLines, [
                            "sku" => $variant_sku,
                            "quantity" => $variant_quantity,
                        ]);
                    }
                }
            } 
        }


        if(count($ProductLines) > 0){
            // initializing
            $resultHTML = '';
            
            // assigning values got from wp options
            $toptex_api_base_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url');
            $toptex_customer_id = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_customer_id');
            $toptex_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key');

            // Customer billing information details
            $billing_first_name = $order->get_billing_first_name();
            $billing_last_name  = $order->get_billing_last_name();
            $billing_company    = $order->get_billing_company();
            $billing_address_1  = $order->get_billing_address_1();
            $billing_address_2  = $order->get_billing_address_2();
            $billing_city       = $order->get_billing_city();
            $billing_state      = $order->get_billing_state();
            $billing_postcode   = $order->get_billing_postcode();
            $billing_country    = $order->get_billing_country();

            $billing_full_name = $billing_first_name . ' ' . $billing_last_name;
            $billing_address = $billing_address_1 . ', ' . $billing_address_2;

            // for putting on toptex order api
            $Reference = parse_url(site_url(), PHP_URL_HOST) . ' - ' . $order_id . ' - ' . $billing_first_name;

			// assigning values got from wp options
			$toptex_api_base_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url');
			$toptex_username = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_username');
			$toptex_password = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_password');
			$toptex_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key');

            // Toptex API Queries
            require_once( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . 'inc/shortcodes/includes/ToptexApiQueries.php');
            // instantiating
            $ApiQuery = new ToptexApiQueries;
			try {
			// sending single product API request to Toptex
			$toptex_api_get_token = $ApiQuery->toptex_api_get_token($toptex_api_base_url, $toptex_username, $toptex_password, $toptex_api_key);
			} catch (PDOException $e) {
				$resultHTML .= "Error: " . $e->getMessage();
			}finally{
				// assigning some useful values got from Toptex API response
				$toptex_api_get_token = json_decode($toptex_api_get_token, true);
				$toptex_api_token = $toptex_api_get_token["token"];
			}
            try {
                // sending create order API request to Toptex
                $toptex_api_create_order = $ApiQuery->toptex_api_create_order( $toptex_api_base_url, $toptex_api_key, $toptex_api_token, json_encode($ProductLines), $billing_full_name, $billing_address, $billing_postcode, $billing_city, $billing_country,  $Reference );
            }catch (PDOException $e) {
                $resultHTML .= "Error: " . $e->getMessage() . PHP_EOL;
            }finally{
                // // for testing purpose 
                // $myfile = fopen( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . "test.txt", "w");
                // $resultHTML .= $toptex_api_create_order . PHP_EOL;
                // $resultHTML .= $Reference . PHP_EOL;
                // $resultHTML .= json_encode($ProductLines);
                // fwrite($myfile, $resultHTML);
                // fclose($myfile);
                return;
            }
        }
    }
    // if is paid ends here
}
// wc successful checkout ends here




// triggers on manually trashing a Toptex Product
add_action( 'wp_trash_post', 'delete_toptex_product', 10, 1 );
function delete_toptex_product( $post_id ){
    // if WC product
    $product = wc_get_product( $post_id );
    if ( !$product ) {
        return;
    }
    $toptex_cron_list = get_option('toptex_cron_list');
    // if toptex product
    if( count($toptex_cron_list) > 0 ){
        $keys = array_keys($toptex_cron_list);
        // checking if toptex product or not
        if (in_array($post_id, $keys)) {
            // remove the item from cron list
            unset($toptex_cron_list[$post_id]);
            // update the option
            update_option('toptex_cron_list', $toptex_cron_list);
			// clean next to update option
			$toptex_cron_list = get_option('toptex_cron_list');
			if( count($toptex_cron_list) == 0 ){
				update_option( 'toptex_sku_next_to_update', '' );
			}
        }
    }else{
		update_option( 'toptex_sku_next_to_update', '' );
	}
}
// triggers on manually trashing a Toptex Product ends here


// triggers on manually un-trashing a Toptex Product
add_action( 'untrash_post', 'un_delete_toptex_product', 10, 1 );
function un_delete_toptex_product( $post_id ){
    // if WC product
    $product = wc_get_product( $post_id );
    if ( !$product ) {
        return;
    }
    $product_id = $product->id;
    $product_sku = $product->sku;

	$toptex_products_sku_list = get_option('toptex_products_sku_list');
	if (in_array($product_sku, $toptex_products_sku_list)) {
		$toptex_cron_list = get_option('toptex_cron_list');
		if (!in_array($product_sku, $toptex_cron_list)){
			$toptex_cron_list[$product_id] = $product_sku;
			// update the option
			update_option('toptex_cron_list', $toptex_cron_list);
		}
	}
}
// triggers on manually un-trashing a Toptex Product ends here


// permanently delete hook
add_action( 'before_delete_post', 'permanently_delete_toptex_product', 10, 1 );
function permanently_delete_toptex_product( $post_id ){
    // if WC product
    $product = wc_get_product( $post_id );
    if ( !$product ) {
        return;
    }
    $toptex_products_sku_list = get_option('toptex_products_sku_list');
    // if toptex product
    if( count($toptex_products_sku_list) > 0 ){
        $keys = array_keys($toptex_products_sku_list);
        // checking if toptex product or not
        if (in_array($post_id, $keys)) {
            // remove the item from cron list
            unset($toptex_products_sku_list[$post_id]);
            // update the option
            update_option('toptex_products_sku_list', $toptex_products_sku_list);
			// clean next to update option
			$toptex_products_sku_list = get_option('toptex_products_sku_list');
			if( count($toptex_products_sku_list) == 0 ){
				update_option( 'toptex_sku_next_to_update', '' );
			}
        }
    }else{
		update_option( 'toptex_sku_next_to_update', '' );
	}
}
// permanently delete hook ends here


// adding new cron task to the system
if(file_exists( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . 'cron.php')){
    // if cron is turned on
    $cron_on_off = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_cron_on_off');
    if($cron_on_off == 'yes'){
        add_filter( 'cron_schedules', function ( $schedules ) {
            $schedules['toptex_per_ten_minutes'] = array(
                'interval' => 600, // ten minutes
                'display' => __( 'Ten Minutes' )
            );
            return $schedules;
        } );
        // cron function starts here
        add_action('toptex_cron_event', 'toptex_cron_function');
        function toptex_cron_function() {
            $resultHTML = '';
            try{
                // run the cron
                $toptex_curl = curl_init();
                curl_setopt($toptex_curl, CURLOPT_URL, WOOCOMMERCE_TOPTEX_API_PLUGIN_URL . 'cron.php');
                curl_exec($toptex_curl);
                if (curl_errno ( $toptex_curl )) {
                    $resultHTML .= date("Y-m-d h:i:sa") . ' - Curl error: ' . curl_error ( $toptex_curl ) . PHP_EOL;
                    // for outputting the error
                    $myfile = fopen( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . "cron-curl-error.txt", "a");
                    fwrite($myfile, $resultHTML);
                    fclose($myfile);
                }
                curl_close($toptex_curl); 
            }catch (PDOException $e) {
                $resultHTML .= date("Y-m-d h:i:sa") . " - Error: " . $e->getMessage() . PHP_EOL;
                // for outputting the error
                $myfile = fopen( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . "cron-curl-error.txt", "a");
                fwrite($myfile, $resultHTML);
                fclose($myfile);
            }finally{
                // for outputting the error
                // $myfile = fopen( WOOCOMMERCE_TOPTEX_API_PLUGIN_PATH . "cron-curl-error.txt", "a");
                // fwrite($myfile, $resultHTML);
                // fclose($myfile);

                // Clear all W3 Total Cache
                if ( function_exists( 'w3tc_flush_all' ) ) {
                    w3tc_flush_all();
                }
            }
        }

        // add cron to the schedule
        if ( ! wp_next_scheduled( 'toptex_cron_event' ) ) {
            wp_schedule_event( time(), 'toptex_per_ten_minutes', 'toptex_cron_event' );
        }

    }else{
        // turn off the cron
        if ( wp_next_scheduled( 'toptex_cron_event' ) ) {
            wp_clear_scheduled_hook( 'toptex_cron_event' );
        }
    }
    // cron function ends here

}else{
    // turn off the cron
    if ( wp_next_scheduled( 'toptex_cron_event' ) ) {
        wp_clear_scheduled_hook( 'toptex_cron_event' );
    }
}
// adding new cron task to the system ends here