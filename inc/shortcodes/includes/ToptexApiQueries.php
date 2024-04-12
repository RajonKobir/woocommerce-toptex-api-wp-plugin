<?php

require_once __DIR__ . '/../../../open-ai/vendor/autoload.php';

use Orhanerday\OpenAi\OpenAi;

//  no direct access 
if( !defined('ABSPATH') ) : exit(); endif;

class ToptexApiQueries
{

    // grabs single product info from toptex
    public function toptex_api_get_token($toptex_api_base_url, $toptex_username, $toptex_password, $toptex_api_key)
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $toptex_api_base_url . 'v3/authenticate',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "username": "'.$toptex_username.'",
            "password": "'.$toptex_password.'"
        }',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: ' . $toptex_api_key,
            'Content-Type: application/json'
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


    // grabs single variation stock info
    public function toptex_api_get_product($toptex_api_base_url, $toptex_api_catalog_reference, $toptex_api_key, $toptex_api_token)
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $toptex_api_base_url . 'v3/products?catalog_reference='.$toptex_api_catalog_reference.'&usage_right=b2b_b2c&display_prices=1',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: ' . $toptex_api_key,
            'x-toptex-authorization: ' . $toptex_api_token
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


    // grabs single variation stock info
    public function toptex_api_all_catalog_references($toptex_api_base_url, $family, $subfamily, $brand, $toptex_api_key, $toptex_api_token)
    {

      // initializing
      $curl_url = '';
      $family = urlencode($family);
      $subfamily = urlencode($subfamily);
      $result = '';

      if($brand == ''){
        $curl_url = $toptex_api_base_url . 'v3/products/all?usage_right=b2b_b2c&display_prices=1&family='.$family.'&subfamily=' . $subfamily;
      }else{
        $brand = urlencode($brand);
        $curl_url = $toptex_api_base_url . 'v3/products/all?usage_right=b2b_b2c&display_prices=1&family='.$family.'&subfamily=' . $subfamily . '&brand=' . $brand;
      }

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $curl_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: ' . $toptex_api_key,
            'x-toptex-authorization: ' . $toptex_api_token
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


    // grabs single variation stock info
    public function toptex_api_variant_stock($toptex_api_base_url, $toptex_api_variant_sku, $toptex_api_key, $toptex_api_token)
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $toptex_api_base_url . 'v3/products/'.$toptex_api_variant_sku.'/inventory',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'x-api-key: ' . $toptex_api_key,
            'x-toptex-authorization: ' . $toptex_api_token
          ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


    // creating a order on toptex
    public function toptex_api_create_order( $toptex_api_base_url, $toptex_api_key, $toptex_api_token, $ProductLines, $Name, $Address, $PostalCode, $City, $Country, $Reference )
    {

      // initializing
      $result = '';

      try {

        // connecting to the API
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $toptex_api_base_url . 'v3/orders',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
          "orderReference":"'.$Reference.'", 
          "deliveryAddress":{
               "addressTitle":"Home Adress",
               "street1":"'.$Address.'",
               "street2":"",
               "postCode":"'.$PostalCode.'",
               "city":"'.$City.'",
               "country":"'.$Country.'",
               "contactName":"'.$Name.'"
            },
          "orderLines":'.$ProductLines.'
        }',
        CURLOPT_HTTPHEADER => array(
          'x-api-key: ' . $toptex_api_key,
          'x-toptex-authorization: ' . $toptex_api_token,
          'Content-Type: application/json'
        ),
        ));
        
        $result = curl_exec($curl);

        if (curl_errno ( $curl )) {
          $result = 'Curl error: ' . curl_error ( $curl );
        }
        
        curl_close($curl);

      } catch (PDOException $e) {

          $result = "Error: " . $e->getMessage();

      }

      return $result;

    }


        // creating a request to OpenAI
        public function open_ai_request_response( $open_ai_api_key, $open_ai_model, $open_ai_prompt, $open_ai_temperature, $open_ai_max_tokens, $open_ai_frequency_penalty, $open_ai_presence_penalty ){

          $result = '';
    
          if(!$open_ai_model || $open_ai_model == ''){
            $open_ai_model = 'text-davinci-003';
          }
          if(!$open_ai_temperature || $open_ai_temperature == ''){
            $open_ai_temperature = 0.9;
          }
          if(!$open_ai_max_tokens || $open_ai_max_tokens == ''){
            $open_ai_max_tokens = 500;
          }
          if(!$open_ai_frequency_penalty || $open_ai_frequency_penalty == ''){
            $open_ai_frequency_penalty = 0;
          }
          if(!$open_ai_presence_penalty || $open_ai_presence_penalty == ''){
            $open_ai_presence_penalty = 0.6;
          }
    
          try {
    
            $open_ai = new OpenAi($open_ai_api_key);
    
            $response = $open_ai->completion([
                'model' => $open_ai_model,
                'prompt' => $open_ai_prompt,
                'temperature' => $open_ai_temperature,
                'max_tokens' => $open_ai_max_tokens,
                'frequency_penalty' => $open_ai_frequency_penalty,
                'presence_penalty' => $open_ai_presence_penalty,
            ]);
    
        } catch (PDOException $e) {
    
            $response = "Error: " . $e->getMessage();
    
        }finally{
    
          $response = json_decode($response, true);
    
          // if no error
          if(!isset($response['error'])){
            if(isset($response["choices"][0]["text"])){
              $result = $response["choices"][0]["text"];
            }
          }
    
        }
    
          return $result;
    
        }
        // end of public function open_ai_request_response 





}