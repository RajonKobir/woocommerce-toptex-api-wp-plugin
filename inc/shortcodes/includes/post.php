<?php

// requiring WC Rest API SDK
require_once __DIR__ . '/../../../wc-api-php-trunk/vendor/autoload.php';
use Automattic\WooCommerce\Client;


// if posted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  // if posted certain values
  if( isset($_POST["toptex_api_catalog_reference"]) && isset($_POST["current_url"]) ){

    // to get the options values
    require_once '../../../../../../wp-config.php';

    // assigning
    $toptex_api_catalog_reference = toptex_secure_input($_POST["toptex_api_catalog_reference"]);
    $current_url = toptex_secure_input($_POST["current_url"]);

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
    $wc_prod_tags = '';

    $open_ai_api_key = '';
    $open_ai_model = '';
    $open_ai_temperature = '';
    $open_ai_max_tokens = '';
    $open_ai_frequency_penalty = '';
    $open_ai_presence_penalty = '';
    $custom_tags_on_off = '';
    $open_ai_on_off = '';

    $variant_price = 0;

    $resultHTML = '';

    // assigning values got from wp options
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_website_url')){
      $website_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_website_url');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_key')){
      $woocommerce_api_consumer_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_key');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret')){
      $woocommerce_api_consumer_secret = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_consumer_secret');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_mul_val')){
      $woocommerce_api_mul_val = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_woocommerce_api_mul_val');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url')){
      $toptex_api_base_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_username')){
      $toptex_username = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_username');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_password')){
      $toptex_password = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_password');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key')){
      $toptex_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_language')){
      $toptex_api_language = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_language');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_wc_prod_tags')){
      $wc_prod_tags = toptex_secure_input(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_wc_prod_tags'));
    }


    // open ai option values
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_api_key')){
      $open_ai_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_api_key');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_model')){
      $open_ai_model = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_model');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_temperature')){
      $open_ai_temperature = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_temperature');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_max_tokens')){
      $open_ai_max_tokens = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_max_tokens');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_frequency_penalty')){
      $open_ai_frequency_penalty = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_frequency_penalty');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_presence_penalty')){
      $open_ai_presence_penalty = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_presence_penalty');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_custom_tags_on_off')){
      $custom_tags_on_off = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_custom_tags_on_off');
    }
    if(get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_on_off')){
      $open_ai_on_off = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_open_ai_on_off');
    }
    

    // assigning language
    $language_full_name = "";
    switch ($toptex_api_language) {
      case "nl":
        $language_full_name = "Dutch";
        break;
      case "en":
        $language_full_name = "English";
        break;
      case "de":
        $language_full_name = "German";
        break;
      case "es":
        $language_full_name = "Spanish";
        break;
      case "fr":
        $language_full_name = "French";
        break;
      case "it":
        $language_full_name = "Italian";
        break;
      case "pt":
        $language_full_name = "Portuguese";
        break;
    }

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
    require_once('ToptexApiQueries.php');

    // instantiating
    $ApiQuery = new ToptexApiQueries;

    try {

      // sending token request to Toptex
      $toptex_api_get_token = $ApiQuery->toptex_api_get_token($toptex_api_base_url, $toptex_username, $toptex_password, $toptex_api_key);

      } catch (PDOException $e) {

        $resultHTML .= "Error: " . $e->getMessage();

      }finally{

        // assigning some useful values got from Toptex API response
        $toptex_api_get_token = json_decode($toptex_api_get_token, true);

        $toptex_api_token = $toptex_api_get_token["token"];

        try {

          // sending single product API request to Toptex
          $toptex_api_get_product = $ApiQuery->toptex_api_get_product($toptex_api_base_url, $toptex_api_catalog_reference, $toptex_api_key, $toptex_api_token);
    
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
              if( count($single_variant["colorsDominant"]) > 0 ){
                $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsDominant"][0][$toptex_api_language];
              }else{
                $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsHexa"][0];
              }

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




// //  open AI starts here 

if($open_ai_api_key && $open_ai_api_key != ''){
  if( $open_ai_on_off == 'yes' ){
    $resultHTML .= '<p class="text-center">OpenAI API has been started...</p>';
    // creating better description
    $open_ai_prompt = 'create a better product description in '.$language_full_name.' from this description &bdquo;'.$toptex_prod_desc.'&bdquo;';
    try {
      // sending request to openAI
      $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
    } catch (PDOException $e) {
      
      $resultHTML .= "Error: " . $e->getMessage();

    }finally{
      $open_ai_request_response = json_decode($open_ai_request_response, true);
      if(isset($open_ai_request_response['error'])){
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product description...</p>';
        $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
      }else{
        if(isset($open_ai_request_response["choices"][0]["text"])){
          $resultText = $open_ai_request_response["choices"][0]["text"];
          $toptex_prod_desc = str_replace('"', '', $resultText);
          $resultHTML .= '<p class="text-center">OpenAI API has updated the product description...</p>';
        }else{
          $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product description. Please contact the developer.</p>';
        }
      }
    }

    // creating better short-description
    $open_ai_prompt = 'create a better product short description in '.$language_full_name.' from this short description &bdquo;'.$toptex_prod_short_desc.'&bdquo;';
    try {
      // sending request to openAI
      $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
    } catch (PDOException $e) {

      $resultHTML .= "Error: " . $e->getMessage();

    }finally{
      $open_ai_request_response = json_decode($open_ai_request_response, true);
      if(isset($open_ai_request_response['error'])){
        $resultHTML .= '<p class="text-center">OpenAI API could not update the product short-description...</p>';
        $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
      }else{
        if(isset($open_ai_request_response["choices"][0]["text"])){
          $resultText = $open_ai_request_response["choices"][0]["text"];
          $toptex_prod_short_desc = str_replace('"', '', $resultText);
          $resultHTML .= '<p class="text-center">OpenAI API has updated the product short-description...</p>';
        }else{
          $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product short-description. Please contact the developer.</p>';
        }
      }
    }


    if( $custom_tags_on_off != 'yes' ){
      $open_ai_prompt = 'create comma separated string of product tags from this description in '.$language_full_name.' &bdquo;'.$toptex_prod_desc.'&bdquo;';
      try {
        // sending request to openAI
        $open_ai_request_response = $ApiQuery->open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty );
      } catch (PDOException $e) {

        $resultHTML .= "Error: " . $e->getMessage();

      }finally{
        $open_ai_request_response = json_decode($open_ai_request_response, true);
        if(isset($open_ai_request_response['error'])){
          $resultHTML .= '<p class="text-center">OpenAI API could not update the product tags...</p>';
          $resultHTML .= '<p class="text-center">OpenAI API Error: '.$open_ai_request_response['error']["type"].' - '.$open_ai_request_response['error']["message"].'</p>';
        }else{
          if(isset($open_ai_request_response["choices"][0]["text"])){
            $resultText = $open_ai_request_response["choices"][0]["text"];
            $wc_prod_tags = toptex_secure_input($resultText);
            $resultHTML .= '<p class="text-center">OpenAI API has updated the product tags...</p>';
          }else{
            $resultHTML .= '<p class="text-center">Unknown OpenAI API Error occured on updating the product tags. Please contact the developer.</p>';
          }
        }
      }
    }
  }
}else{
  $resultHTML .= '<p class="text-center">OpenAI API Key is missing. Started Default Import...</p>';
}

// //  open AI ends here 





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



              // getting all WC product tags
              $retrieved_all_tags = [];
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
                  // getting all WC products
                  $retrieved_all_tags_temp = $woocommerce->get('products/tags', $data);
    
                } catch (PDOException $e) {
    
                  $resultHTML .= "Error: " . $e->getMessage();
          
                } 
    
                $retrieved_all_tags = array_merge($retrieved_all_tags, $retrieved_all_tags_temp);
    
                if( count($retrieved_all_tags_temp) < 100 ){
                  break;
                }
                $page++;
              }
              // infinite loop ends here


              // creating all tags array
              $tag_names_array = [];

              if($retrieved_all_tags){

                if(count($retrieved_all_tags) != 0){

                  foreach($retrieved_all_tags as $key => $single_tag){

                    $tag_names_array[$single_tag->id] = toptex_secure_input($single_tag->name);

                  }

                }

              }

              // creating tags array
              $tags_array = [];
              $final_tag_names_array = [];
              
              // creating the tag if not exists
              if($wc_prod_tags != ''){

                $wc_prod_tags = explode(',' , $wc_prod_tags);

                if(count($wc_prod_tags) > 0){

                  foreach($wc_prod_tags as $key => $single_tag){

                    $tag_key = array_search(toptex_secure_input($single_tag), $tag_names_array);

                    if ($tag_key !== false) {

                      array_push($tags_array,[
                        'id' => $tag_key,
                      ]);
                      array_push($final_tag_names_array, $single_tag);

                      $resultHTML .= '<p class="text-center">Tag ('.$single_tag.') already exists!</p>';

                    }else{

                      $data = [
                          'name' => $single_tag
                      ];

                      try {

                        $wc_create_tag = $woocommerce->post('products/tags', $data);

                      }catch (PDOException $e) {

                        $resultHTML .= "Error: " . $e->getMessage();

                      }finally{

                        array_push($tags_array,[
                          'id' => $wc_create_tag->id,
                        ]);
                        array_push($final_tag_names_array, $wc_create_tag->name);

                        $resultHTML .= '<p class="text-center">Tag ('.$single_tag.') created successfully!</p>';

                      }

                    }

                  }

                }

              }
          // creating the tags ends here


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


            // creating product's meta data
            $product_meta_data_array = [
              [
                  'key' => 'name',
                  'value' => $toptex_prod_name,
              ],
              [
                  'key' => 'description',
                  'value' => $toptex_prod_desc,
              ],
              [
                  'key' => 'short_description',
                  'value' => $toptex_prod_short_desc,
              ],
              [
                  'key' => 'sku',
                  'value' => strval($toptex_prod_sku),
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
                  'key' => 'tags',
                  'value' => implode(",", $final_tag_names_array),
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
                    $image_id = woocommerce_toptex_api_custom_image_file_upload( $toptex_all_image_src_array[$i]['src'], $toptex_all_image_src_array[$i]['name'], $wc_product_id );
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

              // creating product data
              $data = [
                'name' => $toptex_prod_name,
                'type' => 'variable',
                'description' => $toptex_prod_desc,
                'short_description' => $toptex_prod_short_desc,
                'sku' => strval($toptex_prod_sku),
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
                'tags'  => $tags_array,
                'meta_data' =>  $product_meta_data_array
              ];

              try {

                // trying to update a WC product
                $update_wc_prod = $woocommerce->put('products/' . strval($key3), $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                // get the correct product id
                $wc_product_id = $update_wc_prod->id;

                $wc_retrieved_product = $update_wc_prod;

                //create or update wp-option includes list of product sku for cron 
                $product_id = $update_wc_prod->id;
                $product_sku = $update_wc_prod->sku;

                if( $product_id != '' && $product_sku != ''){

                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$toptex_prod_name.') Updated successfully!</p>';

                  $toptex_cron_list = get_option('toptex_cron_list');

                  if ( !in_array($product_sku, $toptex_cron_list) ){

                    $toptex_cron_list[$product_id] = $product_sku;

                    update_option('toptex_cron_list', $toptex_cron_list);
    
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the cron list successfully!</p>';

                  }

                  $toptex_products_sku_list = get_option('toptex_products_sku_list');

                  if ( !in_array($product_sku, $toptex_products_sku_list) ){

                    $toptex_products_sku_list[$product_id] = $product_sku;

                    update_option('toptex_products_sku_list', $toptex_products_sku_list);
    
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the all toptex products list successfully!</p>';

                  }

                  $toptex_sku_next_to_update = get_option('toptex_sku_next_to_update');

                  if($toptex_sku_next_to_update == ''){

                    update_option('toptex_sku_next_to_update', $product_sku );

                    $resultHTML .= '<p class="text-center">Next to update option was empty</p>';
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the next to update cron successfully!</p>';

                  }

                }else{
                  $resultHTML .= '<p class="text-center">Product ('.$toptex_prod_name.') could not be imported!</p>';
                }
                //create or update wp-option includes list of product sku for cron ends here

              }

            }else{

              // creating product data
              $data = [
                  'name' => $toptex_prod_name,
                  'type' => 'variable',
                  'description' => $toptex_prod_desc,
                  'short_description' => $toptex_prod_short_desc,
                  'sku' => strval($toptex_prod_sku),
                  'categories' => [
                      [
                          'id' => (isset($callBack2->id)) ? $callBack2->id : $key2,
                      ],
                      [
                          'id' => (isset($callBack5->id)) ? $callBack5->id : $key5,
                      ],
                  ],
                  // 'images' => $updated_images_array,
                  'attributes'  => $attributes_array,
                  'tags'  => $tags_array,
                  'meta_data' =>  $product_meta_data_array
              ];

              try {

                // trying to create a WC product
                $create_wc_prod = $woocommerce->post('products', $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                // get the correct product id
                $wc_product_id = $create_wc_prod->id;

                $wc_retrieved_product = $create_wc_prod;

                //create or update wp-option includes list of product sku for cron 
                $product_id = $create_wc_prod->id;
                $product_sku = $create_wc_prod->sku;

                if( $product_id != '' && $product_sku != ''){

                  $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$toptex_prod_name.') created successfully!</p>';

                  $toptex_cron_list = get_option('toptex_cron_list');

                  if ( !in_array($product_sku, $toptex_cron_list) ){

                    $toptex_cron_list[$product_id] = $product_sku;

                    update_option('toptex_cron_list', $toptex_cron_list);
    
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the cron list successfully!</p>';

                  }

                  $toptex_products_sku_list = get_option('toptex_products_sku_list');

                  if ( !in_array($product_sku, $toptex_products_sku_list) ){

                    $toptex_products_sku_list[$product_id] = $product_sku;

                    update_option('toptex_products_sku_list', $toptex_products_sku_list);
    
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the all toptex products list successfully!</p>';

                  }

                  $toptex_sku_next_to_update = get_option('toptex_sku_next_to_update');

                  if($toptex_sku_next_to_update == ''){

                    update_option('toptex_sku_next_to_update', $product_sku );

                    $resultHTML .= '<p class="text-center">Next to update option was empty</p>';
                    $resultHTML .= '<p class="text-center">Product '.$product_id.' => '.$product_sku.' => ('.$toptex_prod_name.') has been inserted to the next to update cron successfully!</p>';

                  }

                }else{
                  $resultHTML .= '<p class="text-center">Product ('.$toptex_prod_name.') could not be imported!</p>';
                }
              // product not created ends here


              try {

                // retrieving the product
                $wc_retrieved_product = $woocommerce->get('products/' . strval($wc_product_id));

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{
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
                      $image_id = woocommerce_toptex_api_custom_image_file_upload( $toptex_all_image_src_array[$i]['src'], $toptex_all_image_src_array[$i]['name'], $wc_product_id );
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

                // creating product data
                $data = [
                  'images' => $updated_images_array
                ];

              }


              try {

                // trying to update a WC product
                $update_wc_prod = $woocommerce->put('products/' . strval($wc_product_id), $data);

              }catch (PDOException $e) {

                $resultHTML .= "Error: " . $e->getMessage();

              }finally{

                // get the correct product id
                $wc_product_id = $update_wc_prod->id;

                $wc_retrieved_product = $update_wc_prod;

                //create or update wp-option includes list of product sku for cron 
                $product_id = $update_wc_prod->id;
                $product_sku = $update_wc_prod->sku;

                $resultHTML .= '<p class="text-center">Product ('.$product_id.') => ('.$product_sku.') => ('.$toptex_prod_name.') Images imported successfully!</p>';


              }


            }

          }
          // end of if-else



          if ($wc_product_id != ''){
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
                  if( count($single_variant["colorsDominant"]) > 0 ){
                    $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsDominant"][0][$toptex_api_language];
                  }else{
                    $variant_color = $single_variant["colors"][$toptex_api_language] . '-' . $single_variant["colorsHexa"][0];
                  }

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
                            if($toptex_api_variant_stock != ''){
                              $toptex_api_variant_stock = json_decode($toptex_api_variant_stock, true);
                              if(is_array($toptex_api_variant_stock)){
                                if(count($toptex_api_variant_stock) != 0){
                                  foreach($toptex_api_variant_stock as $stock_key => $single_stock){
                                    if(isset($single_stock["warehouses"])){
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
                          }

                        }
                        // try-catch ends here


                        // variant price
                        if(isset($single_size["prices"][0]["price"])){
                          $variant_price = round((floatval($woocommerce_api_mul_val) * floatval($single_size["prices"][0]["price"])), 2);
                        }


                        $variation_meta_data = [
                          [
                              'key' => 'name',
                              'value' => $toptex_prod_name,
                          ],
                          [
                              'key' => 'price',
                              'value' => strval($variant_price),
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
                          'regular_price' => strval($variant_price),
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
        }else{
          $resultHTML .= '<p class="text-center">Got no product for importing the variations!</p>';
        }

        }

      }

    }else{

      $resultHTML .= '<p class="text-center">Got no results from Toptex!</p>';
  
    }

    }
    // try catch ends here 

  echo $resultHTML;


  }  // if posted certain values ends





// different post
  // if posted certain values
  if( isset( $_POST["family"]) && isset($_POST["subfamily"]) && isset($_POST["brand"]) ){

    // to get the options values
    require_once '../../../../../../wp-config.php';

      // assigning
    $family = toptex_secure_input($_POST["family"]);
    $subfamily = toptex_secure_input($_POST["subfamily"]);
    $brand = toptex_secure_input($_POST["brand"]);

    // initializing
    $toptex_api_base_url = '';
    $toptex_username = '';
    $toptex_password = '';
    $toptex_api_key = '';
    $resultHTML = '';

    // assigning values got from wp options
    $toptex_api_base_url = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_base_url');
    $toptex_username = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_username');
    $toptex_password = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_password');
    $toptex_api_key = get_option( WOOCOMMERCE_TOPTEX_API_PLUGIN_NAME . '_toptex_api_key');


    // Toptex API Queries
    require_once('ToptexApiQueries.php');

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
      $toptex_api_all_catalog_references = $ApiQuery->toptex_api_all_catalog_references($toptex_api_base_url, $family, $subfamily, $brand, $toptex_api_key, $toptex_api_token);
      } catch (PDOException $e) {

        $resultHTML .= "Error: " . $e->getMessage();

      }finally{

        $toptex_api_all_catalog_references = json_decode($toptex_api_all_catalog_references, true);

      }
      // if a valid response
      if(isset($toptex_api_all_catalog_references["items"])){

        $all_toptex_product_items = $toptex_api_all_catalog_references["items"];

        // initializing
        $all_products_references = [];
        foreach( $all_toptex_product_items as $item_key => $single_product_item ){
          $single_catalog_reference = $single_product_item["catalogReference"];
          array_push($all_products_references, $single_catalog_reference);
        }

        // get all available products in WC
        $toptex_products_sku_list = get_option('toptex_products_sku_list');
        if(count($all_products_references) > 0){
          $resultHTML .= '<p class="text-center">Click any items below to import: (Green ones are already imported!)</p>';
          $resultHTML .= '<style>
              .toptex_clickable_catalog{
                  cursor: pointer;
              }
              .toptex_catalog{
                  margin-right: 10px;
                  display: inline-block;
              }
              .toptex_catalog:last-child{
                  margin-right: 0;
              }
          </style>';
          foreach( $all_products_references as $reference_key => $single_catalog_reference ){
            if (in_array($single_catalog_reference, $toptex_products_sku_list)){
              $resultHTML .= '<span class="text-success toptex_catalog">'.$single_catalog_reference.'</span>';
            }else{
              $resultHTML .= '<span class="text-danger toptex_catalog toptex_clickable_catalog">'.$single_catalog_reference.'</span>';
            }
            
          }

          // import all button 
          $resultHTML .= '<br><br>';
          $resultHTML .= '<button type="submit" id="woocommerce_toptex_api_import_all_button" class="woocommerce_toptex_api_import_all_button btn btn-warning mb-3" name="woocommerce_toptex_api_import_all_button">Import All</button>';
  
          $resultHTML .= '
          <script>

            $(".toptex_clickable_catalog").click(function(){
              $("#woocommerce_toptex_api_catalog_reference_field").val($(this).html());
            });

            $(".woocommerce_toptex_api_import_all_button").click(async function(event){
              event.preventDefault();
              let all_products_references = '.json_encode($all_products_references).';
              let toptex_api_catalog_reference;
              let post_url = "' . WOOCOMMERCE_TOPTEX_API_PLUGIN_URL . 'inc/shortcodes/includes/post.php";
              let current_url = $(location).attr("href");
              let toptex_submit_reference_result_button_text = $("#toptex_submit_reference_result").html();
              let woocommerce_toptex_api_submit_button_text = $("#woocommerce_toptex_api_submit_button").html();
              $("#toptex_submit_reference_result").attr("disabled", true);
              $("#toptex_submit_reference_result").html("Importing...");
              $("#woocommerce_toptex_api_submit_button").attr("disabled", true);
              $("#woocommerce_toptex_api_submit_button").html("Importing...");
              $("#result").html("<h6>Please do not refresh or close this window while importing...</h6>");
              $("#result h6").addClass("text-center text-danger");
              
              for (let index = 0; index < all_products_references.length; index++) {
                toptex_api_catalog_reference = all_products_references[index];
                await $.ajax({
                    type: "POST",
                    url: post_url,
                    data: {toptex_api_catalog_reference, current_url}, 
                    success: function(result){
                        $("#result").html(result);
                        if(index < all_products_references.length){
                          $("#toptex_submit_reference_result").attr("disabled", true);
                          $("#toptex_submit_reference_result").html("Importing...");
                          $("#woocommerce_toptex_api_submit_button").attr("disabled", true);
                          $("#woocommerce_toptex_api_submit_button").html("Importing...");
                          $("#result").prepend("<h6>Please do not refresh or close this window while importing...</h6>");
                          $("#result h6").addClass("text-center text-danger");
                        }
                    }
                });
              }

              $("#result").prepend("<h6>All The Selected "+all_products_references.length+" Products Have Been Successfully Imported!</h6>");
              $("#result h6").addClass("text-center text-success");
              $("#toptex_submit_reference_result").attr("disabled", false);
              $("#toptex_submit_reference_result").html(toptex_submit_reference_result_button_text);
              $("#woocommerce_toptex_api_submit_button").attr("disabled", false);
              $("#woocommerce_toptex_api_submit_button").html(woocommerce_toptex_api_submit_button_text);

            });
          </script>
          ';
        }
      }else{
        $resultHTML .= '<p class="text-center">No Catalog References found!</p>';
      }

    echo $resultHTML;

  }





}   // if posted ends

?>