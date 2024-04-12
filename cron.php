<?php

// requiring WC Rest API SDK
require_once  'wc-api-php-trunk/vendor/autoload.php';
use Automattic\WooCommerce\Client;

// to get the options values
require_once '../../../wp-config.php';

// initializing
$website_url = '';
$woocommerce_api_consumer_key = '';
$woocommerce_api_consumer_secret = '';
$woocommerce_api_mul_val = 1;
$toptex_api_base_url = '';
$toptex_username = '';
$toptex_password = '';
$toptex_api_key = '';
$toptex_api_language = '';
$resultHTML = '';

// get option value
$toptex_cron_list = get_option('toptex_cron_list');

if( $toptex_cron_list ){

    if( is_array($toptex_cron_list) ){

        if( count($toptex_cron_list) > 0 ){

            //create or update wp-option includes most recently updated product sku for cron 
            $toptex_sku_next_to_update = get_option('toptex_sku_next_to_update');

            if( $toptex_sku_next_to_update ){

                if( $toptex_sku_next_to_update == '' ){

                    update_option('toptex_sku_next_to_update', $toptex_cron_list[array_keys($toptex_cron_list)[0]] );

                }
            
            }else{

                update_option('toptex_sku_next_to_update', $toptex_cron_list[array_keys($toptex_cron_list)[0]] );

            }

            // updated value
            $toptex_sku_next_to_update = get_option('toptex_sku_next_to_update');

            // if not empty
            if( $toptex_sku_next_to_update != ''){

            // assigning values got from wp options
            $website_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_website_url');
            $woocommerce_api_consumer_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_key');
            $woocommerce_api_consumer_secret = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret');
            $woocommerce_api_mul_val = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_mul_val');
            $toptex_api_base_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url');
            $toptex_username = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_username');
            $toptex_password = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_password');
            $toptex_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key');
            $toptex_api_language = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_language');

            // WC Rest API SDK instantiating
            $woocommerce = new Client(
                $website_url,
                $woocommerce_api_consumer_key,
                $woocommerce_api_consumer_secret,
                [
                    'version' => 'wc/v3',
                ]
            );

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

                // sending single product API request to Toptex
                $toptex_api_get_product = $ApiQuery->toptex_api_get_product($toptex_api_base_url, $toptex_sku_next_to_update, $toptex_api_key, $toptex_api_token);
            
            } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
            }finally{

              $toptex_api_single_product = json_decode($toptex_api_get_product, true);

            }

        // if a valid response
        if(isset($toptex_api_single_product["colors"])){

          $toptex_cat_name = '';
          $toptex_sub_cat_name = '';
          $toptex_prod_variants = [];
          $toptex_prod_name = '';
          $toptex_prod_brand = '';
          $toptex_prod_sku = '';
          $toptex_prod_img = '';
          $toptex_prod_desc = '';
          $toptex_prod_short_desc = '';
          // used in product meta
          $composition = '';
          $fit = '';
          $gender = '';
          $mainMaterials = '';
          $neckType = '';
          $salesArguments = '';
          $threadsFeature = '';
          $typeWeaving = '';

          if (isset($toptex_api_single_product["family"][$toptex_api_language])){
            $toptex_cat_name = $toptex_api_single_product["family"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["sub_family"][$toptex_api_language] )){
            $toptex_sub_cat_name = $toptex_api_single_product["sub_family"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["colors"] )){
            $toptex_prod_variants = $toptex_api_single_product["colors"];
          }
          if (isset( $toptex_api_single_product["designation"][$toptex_api_language] )){
            $toptex_prod_name = $toptex_api_single_product["designation"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["brand"] )){
            $toptex_prod_brand = $toptex_api_single_product["brand"];
          }
          if (isset( $toptex_api_single_product["catalogReference"] )){
            $toptex_prod_sku = $toptex_api_single_product["catalogReference"];
          }
          if (isset( $toptex_api_single_product["images"][0]["url"] )){
            $toptex_prod_img = $toptex_api_single_product["images"][0]["url"];
          }
          if (isset( $toptex_api_single_product["description"][$toptex_api_language] )){
            $toptex_prod_desc = $toptex_api_single_product["description"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["description"][$toptex_api_language] )){
            $toptex_prod_short_desc = $toptex_api_single_product["description"][$toptex_api_language];
          }

          // updated product name
          if(str_contains($toptex_prod_name, $toptex_prod_brand) && !str_contains($toptex_prod_name, $toptex_prod_sku)){
              $toptex_prod_name = $toptex_prod_name . ' #' . $toptex_prod_sku;
          }
          elseif(!str_contains($toptex_prod_name, $toptex_prod_brand) && str_contains($toptex_prod_name, $toptex_prod_sku)){
              $toptex_prod_name = $toptex_prod_brand . ' ' . $toptex_prod_name;
          }
          elseif(!str_contains($toptex_prod_name, $toptex_prod_brand) && !str_contains($toptex_prod_name, $toptex_prod_sku)){
              $toptex_prod_name = $toptex_prod_brand . ' ' . $toptex_prod_name . ' #' . $toptex_prod_sku;
          }

          // used in product meta
          if (isset( $toptex_api_single_product["composition"][$toptex_api_language] )){
            $composition = $toptex_api_single_product["composition"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["fit"][$toptex_api_language] )){
            $fit = $toptex_api_single_product["fit"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["gender"][$toptex_api_language] )){
            $gender = $toptex_api_single_product["gender"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["mainMaterials"][0][$toptex_api_language] )){
            $mainMaterials = $toptex_api_single_product["mainMaterials"][0][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["neckType"][0][$toptex_api_language] )){
            $neckType = $toptex_api_single_product["neckType"][0][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["salesArguments"][$toptex_api_language] )){
            $salesArguments = $toptex_api_single_product["salesArguments"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["threadsFeature"][$toptex_api_language] )){
            $threadsFeature = $toptex_api_single_product["threadsFeature"][$toptex_api_language];
          }
          if (isset( $toptex_api_single_product["typeWeaving"][$toptex_api_language] )){
            $typeWeaving = $toptex_api_single_product["typeWeaving"][$toptex_api_language];
          }
        

        // $toptex_atrributes = ["Brand", "Color", "Size", "BodyLengthWidth"];
        $toptex_atrributes = ["Color", "Size"];

        $toptex_all_colors_array = [];
        $toptex_all_sizes_array = [];

        //initially putting first image
        $toptex_all_image_src_array = [
          [
            'src' => $toptex_prod_img,
            'name' => $toptex_prod_name,
            'alt' => $toptex_prod_name,
          ],
        ];

        // creating color, size, images array
        if(count($toptex_prod_variants) > 0){
          foreach($toptex_prod_variants as $key_variant => $single_variant){
            // initializing
            $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsDominant"][0][$toptex_api_language];

              if( $single_variant["saleState"] == "active" ){

                if($variant_color != '' && !in_array($variant_color, $toptex_all_colors_array)){

                  array_push($toptex_all_colors_array, $variant_color);

                }


                $temp_image_name = $toptex_prod_name . ' - ' . $variant_color;

                // creating product images array
                if(isset($single_variant["packshots"]["FACE"]["url"])){
                  if($single_variant["packshots"]["FACE"]["url"] != ''){
                    $temp_image_src = $single_variant["packshots"]["FACE"]["url"];
                  }
                }else{
                  $temp_image_src = $toptex_api_single_product["images"][$key_variant + 1]["url"];
                }

                $tem_toptex_array = [
                  'src' => $temp_image_src,
                  'name' => $temp_image_name,
                  'alt' => $temp_image_name,
                ];
        
                array_push($toptex_all_image_src_array,  $tem_toptex_array);


                foreach($single_variant["sizes"] as $key_size => $single_size){
                  if($single_size["saleState"] == "active"){
                    if($single_size["size"] != '' && !in_array($single_size["size"], $toptex_all_sizes_array)){
                      array_push($toptex_all_sizes_array, $single_size["size"]);
                    }
                  }
                }

              }

            }
          }
          // end of foreach



    
          // var_dump($toptex_all_image_src_array);


          // getting all WC categories
          $product_category_list = [];

          // initializing
          $page = 1;

          // infinite loop
          while(1 == 1) {

            // initializing for grabbing all categories
            $data = [
              'page' => $page,
              'per_page' => 100,
            ];

            try{
              // getting all WC categories
              $product_category_list_temp = $woocommerce->get('products/categories', $data);

            } catch (PDOException $e) {

              $resultHTML .= "Error: " . $e->getMessage();
      
            } 

            $product_category_list = array_merge($product_category_list, $product_category_list_temp);

            if( count($product_category_list_temp) < 100 ){
              break;
            }

            $page++;

          }
          // infinite loop ends here


            // creating all category names array
            $product_category_names = [];

            foreach($product_category_list as $key => $single_category){

              $product_category_names[$single_category->id] = toptex_secure_input($single_category->name);

            }

            // checking category names exist or not
            $key1 = array_search(toptex_secure_input($toptex_cat_name), $product_category_names);

            // creating WC category
            if ($key1 !== false) {

              $resultHTML .= '<p class="text-center">Category ('.$toptex_cat_name.') already exists!</p>';

            }else{

              $category = [
                  'name' => $toptex_cat_name
              ];

              try {

                $callBack1 = $woocommerce->post('products/categories', $category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Category ('.$toptex_cat_name.') has been created!</p>';

              }

            }


            // checking sub-category names exist or not
            $key2 = array_search(toptex_secure_input($toptex_sub_cat_name), $product_category_names);

            // creating WC sub-category
            if ($key2 !== false) {

              $resultHTML .= '<p class="text-center">Sub-Category ('.$toptex_sub_cat_name.') already exists!</p>';

            }else{

              $sub_category = [
                  'name' => $toptex_sub_cat_name,
                  'parent' => (isset($callBack1->id)) ? $callBack1->id : $key1
              ];

              try {

                $callBack2 = $woocommerce->post('products/categories', $sub_category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Sub-Category ('.$toptex_sub_cat_name.') has been created!</p>';

              }

            }


            // checking sub-category names exist or not
            $key5 = array_search(toptex_secure_input($toptex_prod_brand), $product_category_names);

            // creating WC sub-category
            if ($key5 !== false) {

              $resultHTML .= '<p class="text-center">Sub-Category ('.$toptex_prod_brand.') already exists!</p>';

            }else{

              $sub_category = [
                  'name' => $toptex_prod_brand,
                  'parent' => (isset($callBack1->id)) ? $callBack1->id : $key1
              ];

              try {

                $callBack5 = $woocommerce->post('products/categories', $sub_category);

              } catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{

                $resultHTML .= '<p class="text-center">Sub-Category ('.$toptex_prod_brand.') has been created!</p>';

              }

            }
            // creating category and sub-category ends here



            try {

                // getting all WC attributes
                $wc_all_attributes = $woocommerce->get('products/attributes');
  
              }catch (PDOException $e) {
  
                $resultHTML .= "Error: " . $e->getMessage();
  
              }finally{
  
                // creating all attributes array
                $wc_all_attributes_array = [];
  
                if(count($wc_all_attributes) != 0){
  
                  foreach($wc_all_attributes as $id => $single_attribute){
  
                    $wc_all_attributes_array[$single_attribute->id] = $single_attribute->name;
  
                  }
  
                }
  
  
                // loop through all attributes & create if not exists
                foreach($toptex_atrributes as $key => $single_attribute){
  
                  if(count($wc_all_attributes_array) != 0){
                    
                    if(!in_array($single_attribute, $wc_all_attributes_array)){
  
                      $data = [
                          'name' => $single_attribute,
                          'slug' => str_replace(' ', '_', $single_attribute),
                          'type' => 'select',
                          'order_by' => 'menu_order',
                          'has_archives' => true
                      ];
  
                      try {
  
                        $wc_create_attribute = $woocommerce->post('products/attributes', $data);
  
                      } catch (PDOException $e) {
  
                        $resultHTML .= "Error: " . $e->getMessage();
                
                      }finally{
  
                        $resultHTML .= '<p class="text-center">Attribute ('.$single_attribute.') created successfully!</p>';
  
                      }
                      
                    }
  
                  }else{
  
                  $data = [
                      'name' => $single_attribute,
                      'slug' => str_replace(' ', '_', $single_attribute),
                      'type' => 'select',
                      'order_by' => 'menu_order',
                      'has_archives' => true
                  ];
  
                  try {
  
                    $wc_create_attribute = $woocommerce->post('products/attributes', $data);
  
                  } catch (PDOException $e) {
  
                    $resultHTML .= "Error: " . $e->getMessage();
            
                  }finally{
  
                    $resultHTML .= '<p class="text-center">Attribute ('.$single_attribute.') created successfully!</p>';
  
                  }
  
                }
  
              }
              // create attribute ends here
  
        
              try {
  
                // getting all attributes again
                $wc_all_attributes = $woocommerce->get('products/attributes');
      
              }catch (PDOException $e) {
        
                $resultHTML .= "Error: " . $e->getMessage();
        
              }finally{
  
                // creating avilable attributes array
                $wc_all_attributes_array = [];
  
                if(count($wc_all_attributes) != 0){
  
                  foreach($wc_all_attributes as $id => $single_attribute){
  
                    $wc_all_attributes_array[$single_attribute->id] = $single_attribute->name;
  
                  }
  
                }

            


              // getting all WC products
              $wc_all_products = [];
              // initializing
              $page = 1;
              // infinite loop
              while(1 == 1) {
                // initializing for grabbing all products
                $data = [
                  'page' => $page,
                  'per_page' => 100,
                ];
                try{
                  // getting all WC products
                  $all_products_list_temp = $woocommerce->get('products',  $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $wc_all_products = array_merge($wc_all_products, $all_products_list_temp);
    
                if( count($all_products_list_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


          
                  // creating all products array
                  $wc_all_prod_array = [];

                  if($wc_all_products){

                    if(count($wc_all_products) != 0){

                      foreach($wc_all_products as $key => $single_wc_prod){

                        $wc_all_prod_array[$single_wc_prod->id] = $single_wc_prod->sku;

                      }
                      
                    }

                  }


            // attempting to create or update a product
            // creating attributes array
            $attributes_array = [];

            if(count($toptex_all_colors_array) != 0 ){
              array_push($attributes_array,[
                'id'        => array_search("Color", $wc_all_attributes_array),
                'variation' => true,
                'visible'   => true,
                'options'   => $toptex_all_colors_array,
              ]);
            }


            if(count($toptex_all_sizes_array) != 0 ){
              array_push($attributes_array,[
                'id'        => array_search("Size", $wc_all_attributes_array),
                'variation' => true,
                'visible'   => true,
                'options'   => $toptex_all_sizes_array,
              ]);
            }


            // if product sku exists or not
            $key3 = array_search($toptex_prod_sku, $wc_all_prod_array);

            if ($key3 !== false) {

              // get the correct product id
              $wc_product_id = $key3;

              try {

                // retrieving the product
                $wc_retrieved_product = $woocommerce->get('products/' . strval($wc_product_id));

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }

              // initializing
              $wc_total_images = count($wc_retrieved_product->images);
              $toptex_total_images = count($toptex_all_image_src_array);
              $missed_images_array = [];

              if($wc_total_images == 0){
                $updated_images_array = [];
              }else{
                $updated_images_array = $wc_retrieved_product->images;
              }
              
              // adding images
              if($toptex_total_images > $wc_total_images){
                for($i = $wc_total_images; $i < $toptex_total_images; $i++){
                  try {
                    $image_id = woocommerce_toptex_api_custom_image_file_upload( $toptex_all_image_src_array[$i]['src'], $toptex_all_image_src_array[$i]['name'] );
                  }catch (PDOException $e) {
                    $resultHTML .= "Error: " . $e->getMessage();
                  }finally{
                    if(is_int($image_id)){
                      array_push($updated_images_array,  [
                        'id' => $image_id,
                        'name' => $toptex_all_image_src_array[$i]['name'],
                        'alt' => $toptex_all_image_src_array[$i]['alt'],
                      ]);
                    }else{
                      array_push($missed_images_array, $i + 1);
                    }
                  }
                }
              }


            // creating product's meta data
            $product_meta_data_array = [
              [
                  'key' => 'name',
                  'value' => $toptex_prod_name,
              ],
              [
                  'key' => 'category',
                  'value' => $toptex_cat_name,
              ],
              [
                  'key' => 'sub_category',
                  'value' => $toptex_sub_cat_name,
              ],
              [
                  'key' => 'brand',
                  'value' => $toptex_prod_brand,
              ],
              [
                  'key' => 'colors',
                  'value' => implode(",", $toptex_all_colors_array),
              ],
              [
                  'key' => 'sizes',
                  'value' => implode(",", $toptex_all_sizes_array),
              ],
              [
                  'key' => 'composition',
                  'value' => $composition,
              ],
              [
                  'key' => 'fit',
                  'value' => $fit,
              ],
              [
                  'key' => 'gender',
                  'value' => $gender,
              ],
              [
                  'key' => 'main_materials',
                  'value' => $mainMaterials,
              ],
              [
                  'key' => 'neck_type',
                  'value' => $neckType,
              ],
              [
                  'key' => 'sales_arguments',
                  'value' => $salesArguments,
              ],
              [
                  'key' => 'threads_feature',
                  'value' => $threadsFeature,
              ],
              [
                  'key' => 'type_weaving',
                  'value' => $typeWeaving,
              ],
            ];


              // creating product data
              $data = [
                'name' => $toptex_prod_name,
                'categories' => [
                    [
                        'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                    ],
                    [
                        'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                    ],
                ],
                'images' => $updated_images_array,
                'attributes'  => $attributes_array,
                'meta_data' => $product_meta_data_array
              ];

              try {

                // trying to update a WC product
                $update_wc_prod = $woocommerce->put('products/' . strval($key3), $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                $wc_retrieved_product = $update_wc_prod;

                $resultHTML .= '<p class="text-center">Product ('.$toptex_prod_name.') updated successfully!</p>';

              }



              // getting all WC product variations
              $wc_all_product_variations = [];
              // initializing
              $page = 1;
              // infinite loop
              while(1 == 1) {
                // initializing for grabbing all products
                $data = [
                  'page' => $page,
                  'per_page' => 100,
                ];
                try{
                  // getting all WC products
                  $wc_all_product_variations_temp = $woocommerce->get('products/'.strval($wc_product_id).'/variations', $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $wc_all_product_variations = array_merge($wc_all_product_variations, $wc_all_product_variations_temp);
    
                if( count($wc_all_product_variations_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


              // creating WC variations sku array
              $wc_variations_sku_array = [];

              if($wc_all_product_variations){

                if(count($wc_all_product_variations) != 0){

                  foreach($wc_all_product_variations as $key => $single_variation){

                    $single_variation_id = $single_variation->id;
                    $single_variation_sku = $single_variation->sku;

                    $wc_variations_sku_array[$single_variation_id] = $single_variation_sku;

                  }

                }

              }




            // initializing
            $variation_image_id = 0;
            $variation_iteration = 0;

            foreach($toptex_prod_variants as $single_key => $single_variant){
        
                if( $single_variant["saleState"] == "active" ){

                // increament
                if (!in_array($variation_image_id + 1, $missed_images_array)){
                    $variation_image_id++;
                }

                // initializing
                $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsDominant"][0][$toptex_api_language];

                foreach($single_variant["sizes"] as $key_size => $single_size){

                    if($single_size["saleState"] == "active"){

                      // creating variation name and sku
                      $single_variant_name = $toptex_prod_name . ' - ' .  $variant_color . ' - ' . $single_size["size"];
                      $toptex_api_variant_sku = $single_size["sku"];
                      // creating variant name
                      if(!str_contains($single_variant_name, $toptex_api_variant_sku) ){
                          $single_variant_name = $single_variant_name . ' #' . $toptex_api_variant_sku;
                      }

                    // creating attributes array
                    $variation_attributes_array = [];

                    if($variant_color != '' ){

                        array_push($variation_attributes_array,[
                        'id' => array_search("Color", $wc_all_attributes_array),
                        'option' => $variant_color,
                        ]);

                    }


                    if($single_size["size"] != '' ){

                        array_push($variation_attributes_array,[
                        'id' => array_search("Size", $wc_all_attributes_array),
                        'option' => $single_size["size"],
                        ]);

                        }

                        try {
                        
                        // sending single product API request to Toptex
                        $toptex_api_variant_stock = $ApiQuery->toptex_api_variant_stock($toptex_api_base_url, $toptex_api_variant_sku, $toptex_api_key, $toptex_api_token);

                        } catch (PDOException $e) {
                
                        $resultHTML .= "Error: " . $e->getMessage();
                
                        }finally{

                        //initializing
                        $toptex_api_stock_quantity = 0;

                        if(isset($toptex_api_variant_stock)){
                            $toptex_api_variant_stock = json_decode($toptex_api_variant_stock, true);
                            if(is_array($toptex_api_variant_stock)){
                            if(count($toptex_api_variant_stock) != 0){
                                foreach($toptex_api_variant_stock as $stock_key => $single_stock){
                                if(is_array($single_stock["warehouses"])){
                                    if(count($single_stock["warehouses"]) != 0){
                                    foreach($single_stock["warehouses"] as $warehouse_key => $single_warehouse){
                                        if(isset($single_warehouse["stock"])){
                                        if(is_int($single_warehouse["stock"])){
                                            $toptex_api_stock_quantity += $single_warehouse["stock"];
                                        }
                                        }
                                    }
                                    }
                                }
                                }
                            }
                            }
                        }

                        }
                        // try-catch ends here

                        // variant price
                        $variant_price = strval(round((floatval($woocommerce_api_mul_val) * floatval($single_size["prices"][0]["price"])), 2));

                        $variation_meta_data = [
                          [
                              'key' => 'name',
                              'value' => $toptex_prod_name,
                          ],
                          [
                              'key' => 'price',
                              'value' => $variant_price,
                          ],
                          [
                              'key' => 'description',
                              'value' => $single_variant_name,
                          ],
                          [
                              'key' => 'sku',
                              'value' => strval($toptex_api_variant_sku),
                          ],
                          [
                              'key' => 'category',
                              'value' => $toptex_cat_name,
                          ],
                          [
                              'key' => 'sub_category',
                              'value' => $toptex_sub_cat_name,
                          ],
                          [
                              'key' => 'brand',
                              'value' => $toptex_prod_brand,
                          ],
                          [
                              'key' => 'bar_code',
                              'value' => $single_size["barCode"],
                          ],
                          [
                              'key' => 'box_height',
                              'value' => $single_size["boxHeight"],
                          ],
                          [
                              'key' => 'box_length',
                              'value' => $single_size["boxLength"],
                          ],
                          [
                              'key' => 'box_width',
                              'value' => $single_size["boxWidth"],
                          ],
                          [
                              'key' => 'color_code',
                              'value' => $single_size["colorCode"],
                          ],
                          [
                              'key' => 'country_of_origin',
                              'value' => implode(",", $single_size["countryOfOrigin"]),
                          ],
                          [
                              'key' => 'customs_code',
                              'value' => $single_size["customsCode"],
                          ],
                          [
                              'key' => 'customs_weight',
                              'value' => $single_size["customsWeight"],
                          ],
                          [
                              'key' => 'ean',
                              'value' => $single_size["ean"],
                          ],
                          [
                              'key' => 'gross_box_weight',
                              'value' => $single_size["grossBoxWeight"],
                          ],
                          [
                              'key' => 'size',
                              'value' => $single_size["size"],
                          ],
                          [
                              'key' => 'size_code',
                              'value' => $single_size["sizeCode"],
                          ],
                          [
                              'key' => 'size_country',
                              'value' => $single_size["sizeCountry"][$toptex_api_language],
                          ],
                        ];
                        
                        // creating variation data
                        $data = [
                          'regular_price' => $variant_price,
                          'description' => $single_variant_name,
                          'sku' => strval($toptex_api_variant_sku),
                          'image' => [
                            'id' => $wc_retrieved_product->images[$variation_image_id]->id,
                          ],
                          'attributes' => $variation_attributes_array,
                          'manage_stock' => true,
                          'stock_quantity' => $toptex_api_stock_quantity,
                          'meta_data' => $variation_meta_data
                        ];

                        $variation_iteration++;

                        // if variation sku exists or not
                    $key4 = array_search($toptex_api_variant_sku, $wc_variations_sku_array);

                    if ($key4 !== false) {

                        try {

                        // updating a variant
                        $wc_create_or_update_variant = $woocommerce->put('products/'.$wc_product_id.'/variations/' . strval($key4), $data);

                        } catch (PDOException $e) {
                
                        $resultHTML .= "Error: " . $e->getMessage();
                
                        }finally{

                        $resultHTML .= '<p class="text-center">Variant '.$variation_iteration.' ('.$single_variant_name.') updated successfully!</p>';

                        }


                    }else{

                        try {

                        // creating a variant
                        $wc_create_or_update_variant = $woocommerce->post('products/'.$wc_product_id.'/variations', $data);

                        } catch (PDOException $e) {
                
                        $resultHTML .= "Error: " . $e->getMessage();
                
                        }finally{

                        $resultHTML .= '<p class="text-center">Variant '.$variation_iteration.' ('.$single_variant_name.') created successfully!</p>';

                        }

                    }

                    }

                    }
                    // end of foreach
                }

            }
            // end of foreach






            // update the wp option for next to update sku

            $key6 = array_search($toptex_sku_next_to_update, $toptex_cron_list);

            $keys = array_keys($toptex_cron_list);

            $key7 =  array_search($key6, $keys);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$toptex_sku_next_to_update.' updated successfully!</p>';

            if( $key7 == (count($keys) - 1) ){

                $next_item = 0;

                $next_key = $keys[$next_item];

            }else{

                $next_item = $key7 + 1;

                $next_key = $keys[$next_item];

            }

            // update option
            update_option('toptex_sku_next_to_update', $toptex_cron_list[$next_key] );

            $resultHTML .= '<p class="text-center">Next to update product SKU: '.($next_item + 1).' => '.$next_key.' =>  '.$toptex_cron_list[$next_key].'</p>';

            // if product found ends here
            // if product not found starts here
        }else{

            $key6 = array_search($toptex_sku_next_to_update, $toptex_cron_list);

            $keys = array_keys($toptex_cron_list);

            $key7 =  array_search($key6, $keys);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$toptex_sku_next_to_update.' could not be found!</p>';

            $resultHTML .= '<p class="text-center">Please Manually import: Product Name  =>  '.$toptex_prod_name.' , Product SKU =>  '.$toptex_prod_sku.'</p>';

            if( $key7 >= (count($keys) - 1) ){

                $next_item = 0;

                $next_key = $keys[$next_item];

            }else{

                $next_item = $key7 + 1;

                if(array_key_exists($next_item, $keys)){

                    $next_key = $keys[$next_item];

                }else{

                    $next_item = 0;

                    $next_key = $keys[$next_item];

                }

            }

            // update option
            update_option('toptex_sku_next_to_update', $toptex_cron_list[$next_key] );

            // remove the unfound item 
            unset($toptex_cron_list[$key6]);

            // update the option
            update_option('toptex_cron_list', $toptex_cron_list);

            $resultHTML .= '<p class="text-center">Product SKU: '.($key7 + 1).' =>  '.$key6.' =>  '.$toptex_sku_next_to_update.' has been removed from the cron list!</p>';

            // get option value
            $toptex_cron_list = get_option('toptex_cron_list');

            if(count($toptex_cron_list) > 0){

                $resultHTML .= '<p class="text-center">Next to update product SKU: '.($next_item + 1).' => '.$next_key.' =>  '.$toptex_cron_list[$next_key].'</p>';

            }else{

                $resultHTML .= '<p class="text-center">Cron List Has Been Emptied!</p>';

            }

            
        }

    }

}

        }else{

            $resultHTML .= '<p class="text-center">Got no results from Toptex!</p>';

        }

            }else{

                $resultHTML .= '<p class="text-center">No Toptex Products To Update!</p>';

            }

        }else{

            // clean the option
            update_option('toptex_sku_next_to_update', '' );

            $resultHTML .= '<p class="text-center">No Toptex Products Found!</p>';

        }

    }else{

        // clean the option
        update_option('toptex_sku_next_to_update', '' );

        $resultHTML .= '<p class="text-center">No Toptex Products Found!</p>';

    }

}else{

    // clean the option
    update_option('toptex_sku_next_to_update', '' );

    $resultHTML .= '<p class="text-center">No Toptex Products Found!</p>';

}


// return results
echo $resultHTML;