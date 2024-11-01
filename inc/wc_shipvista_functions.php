<?php

namespace Shipvista\Functions;

use Exception;

trait SLSR_WcShipvistaFunctions
{
  public $registeredCarriers = ['CanadaPost', 'UPS', 'CANPAR', 'USPS'];
  private $baseApiUrl = 'https://api.shipvista.com/api/';
  public $apiHttpErrorCode;
  public $errorLogKeys = ['Authentication'];
  public $orderId;
  public $carrierDetails = [
    'CanadaPost' => [
      'name' => 'Canada Post',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/canada_post.jpg'
    ],
    'UPS' => [
      'name' => 'UPS',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/ups_logo.png'
    ],
    'USPS' => [
      'name' => 'USPS',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/usps_logo.png'
    ],
    'CANPAR' => [
      'name' => 'CANPAR',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/canpar_logo.png'
    ]
  ];

  function generatePdfFromByte($order_id, $byte)
  {
    try {
      //Write data back to pdf file
      $file = SHIPVISTA__PLUGIN_DIR . "/assets/labels/" . $order_id . "_shipvista_label.pdf";
      if (!file_exists($file)) {
        $pdf_content = $byte;
        $pdf_decoded = base64_decode($pdf_content);
        $pdf = fopen($file, 'w');
        fwrite($pdf, $pdf_decoded);
        //close output file
        fclose($pdf);
      }
      $file = str_replace(SHIPVISTA__PLUGIN_DIR, SHIPVISTA__PLUGIN_URL, $file);
      return $file;
    } catch (Exception $e) {
      $this->SLSR_pluginLogs('labels', $e->getMessage());
      return false;
    }
  }


  public function customShippingItems(array $list)
  {
    $result = [];
    foreach ($list as $key => $values) {
      $quantity = @$values['quanity'] ?: 1;
      $_product =  wc_get_product($values->get_product_id());
      $price = $_product->get_price(); //
      $title = $_product->get_title();
      $weight = $_product->get_weight() ?: ($this->get_option('shipvista_dimension_weight') > 0 ?  $this->get_option('shipvista_dimension_weight') * $quantity :  1);
      $result[] =  [
        'Description' => $title,
        'NumberOfUnits' => $quantity,
        'ValuePerUnit' => $price,
        'Weight' => $weight,
      ];
    }
    return $result;
  }


  function loopAddressBook(array $destination)
  {
    $addresses = $this->get_option("shipvista_address_book") ?: '[]';
    try {

      $match = '';
      $addresses = (array) json_decode($addresses, true);
      if (count($addresses) > 0) {
        foreach ($addresses as $key => $address) {
          $toAddress = $address['to_address'];
          if (empty($toAddress['country']) && empty($toAddress['state']) && empty($toAddress['zip_code'])) { // any address 
            $match = $key;
            break;
          } else {
            // $state = extract($address['to_address'], EXTR_PREFIX_SAME, 'CA');
            // extract
            if (isset($destination['countryCode'])) {
              $fromCountry = strtoupper($destination['countryCode']);
              $countryPass = 1;
              $country = $toAddress['country'];
              if (!empty($country)) {
                $country = is_array($country) ? $country : explode(',', str_replace(' ', '', strtoupper(trim($country))));
                if (!in_array($fromCountry, $country)) {
                  $countryPass = 0;
                }
              }
              $statePass = 1;
              $zipcodePass = 0;

              if ($countryPass) {
                $fromState = strtoupper($destination['stateCode']);
                $state = isset($toAddress['state'][$fromCountry]) ? $toAddress['state'][$fromCountry] : [];
                if (!empty($state)) {
                  $state = $state;
                  if (!in_array($fromState, $state)) {
                    $statePass = 0;
                  }
                }

                if ($statePass) {
                  $fromZipcode = strtoupper($destination['postalCode']);
                  $zip_code = $toAddress['zip_code'];

                  if (!empty($zip_code)) {
                    $zipcodes = explode(',', str_replace(' ', '', strtoupper(trim($zip_code))));
                    foreach ($zipcodes as $zipcode) {
                      $length = strlen($zipcode);
                      if (strtolower(substr($fromZipcode, 0, $length)) == strtolower($zipcode)) {
                        $zipcodePass = 1;
                        break;
                      }
                    }
                  } else {
                    $zipcodePass = 1;
                  }
                }
              }

              if ($countryPass == 1 && $statePass == 1 && $zipcodePass == 1) {
                $match = $key;
                break;
              }
            }
          }
        }

        if (array_key_exists($match, $addresses)) {
          $addressData = $addresses[$match]['from_address'];
          return [
            'postalCode' =>   $addressData['zip_code'],
            'countryCode' => $addressData['country'],
            'stateCode' => $addressData['state'],
            'city' => (isset($addressData['city']) ? $addressData['city'] : ''),
            'residential' => true
          ];
        }
      }

      return false;
    } catch (Exception $e) {
      return false;
    }
  }

  /**
   * Get user shipping from address
   */
  public function getShipFromAddress(array $destination)
  {
    // get countries
    $countryObject = $this->loopAddressBook($destination);

    if (is_array($countryObject) && isset($countryObject['postalCode'])) {
      return $countryObject;
    } else {
      // The country/state
      $store_raw_country = get_option('woocommerce_default_country');
      // Split the country/state
      list($country, $state) = explode(":", $store_raw_country);
      $postcode = get_option('woocommerce_store_postcode');
      if (isset($country) && isset($postcode)) {
        $city = get_option('woocommerce_store_city');
        return [
          'postalCode' =>  str_replace(' ', '', $postcode),
          'countryCode' => strtoupper($country),
          'stateCode' => $state,
          'city' => $city,
          'residential' => true
        ];
      }
    }

    return false;
  }

  public function orderShippingAddress($order)
  {
    if (is_object($order)) {
      $result = [
        'postalCode' => str_replace(' ', '', $order->get_shipping_postcode()),
        'countryCode' => $order->get_shipping_country(),
        'state' => $order->get_shipping_state(),
        'city' => $order->get_shipping_city(),
        'streetAddress' => $order->get_shipping_address_1(),
        'streetAddress2' => $order->get_shipping_address_2(),
        'residential' => true
      ];

      return $result;
    }
    return false;
  }


  function structureLableText($text)
  {
    if ($text) {

      $title = ['recommended' => 'primary', 'fastest' => 'warning', "cheapest" => 'success'];
      $split = explode(':', $text);
      $badge = '';
      for ($index = 0; $index < count($split); $index++) {
        $element = $split[$index];
        if (count($split) > 1 && array_key_exists(strtolower($element), array_keys($title)) >= 0) {
          $bsClass = $title[strtolower($element)];
          $badge .= '<small class="sv_badge sv_badge-' . $bsClass . '">' . $element . '</small>';
          array_splice($split, $index, 1);
        }
      }

      // check if there is a discount
      if (count($split) > 1) {
        $has = [];
        for ($index2 = 0; $index2 < count($split); $index2++) {
          $element2 = $split[$index2];
          $split2 = explode('%', $element2);

          if (count($split2) > 1 && count($has) == 0) {
            $badge .= '<small class=""> ' . $element2 . '</small>';
            array_push($has, $index2);
          } else if (count($split2) > 1) {
            array_push($has, $index2);
          }
        }

        for ($i = 0; $i < count($has); $i++) {
          $element = $has[$i];
          $split[$element] = '';
        }
      }

      if (strlen($badge) > 0) {
        $badge .= '<br>';
      }


      $lable =  join(' ', $split);
      $this->viewShippingLabel = $lable;
      return $badge . $lable;
    }
  }



  public function structOrderFieldList($rates, $activeLabel)
  {
    $form = '';

    $label = (string) wc_get_order_item_meta($this->orderId, 'shipvista_shipment_label') ?: '';
    if (strlen($label) > 3) {
      $byteFile = $this->shipvistaApi("/Shipments/GetLabel", ['fileName' => $label], 'GET');
      $this->SLSR_pluginLogs('byte', json_encode($label));
      if ($byteFile['status'] == true) {
        $labelFile = $this->generatePdfFromByte($this->orderId, $byteFile['data']['fileContents']);
        $tracking = wc_get_order_item_meta($this->orderId, 'shipvista_tracking_number');
        $form = '

      <div class="">
        <div class="mb-3 border-bottom">
        <small>TRACKING NUMBER</small>
        <h4><a class="text-dark" target="_blank" href="https://shipvista.com/track-shipment?trackingnumber=' . $tracking . '">' . $tracking . '</a></h4>
        </div>
        <div class="">
        <small>LABEL</small>
          <iframe src="' . $labelFile . '" style="width:100%;height:300px;" frameborder="0"></iframe>
        </div>
      </div>
      ';
      }
    } else {
      if (is_array($rates) && count($rates) > 0) {
        $currency = $this->get_option('shipvista_user_currency');
        $countRates = 0;

        foreach ($rates as $key => $rate) {
          $cost = $rate['cost'];
          if ($cost > 0) {
            $label = $this->structureLableText($rate['label']);

            $labelSplit = explode(':', $activeLabel);
            $labelView = end($labelSplit);
            $checked = '';
            if (substr(trim($labelView), 0, 10) == substr(trim($this->viewShippingLabel), 0, 10)) {
              $checked = 'checked';
            }
            $class = '';
            if ($countRates > 2) {
              $class = 'shipvista_list_hide sv_d-none';
            }

            $form .= '
         <li class="sv_list-group-item sv_m-0 ' . $class . '">
            <div class="sv_d-flex">
                <div class="sv_px-2 sv_align-self-center">
                    <input type="radio" name="shipvista_shipping_method" ' . $checked . ' data-carrier-option=\'' . (json_encode($rate['options'])) . '\' data-carrier="' . $rate['carrierId'] . '" data-index="' . $key . '" id="shipping_method_' . $key . '_shipvista_dom-rp" value="' . $rate['code'] . '" class="sv_radio" >
                </div>
                <div class="sv_flex-fill  sv_align-self-center">
                    <label for="shipping_method_' . $key . '_shipvista_dom-rp">' . $label . '</label>
                </div>
                <div class="sv_align-self-center sv_pl-2">
                    <b>
                    <span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">' . $currency . '$</span>' . $cost . '</bdi></span>
                    </b>
                </div>
            </div>
        </li>
         ';
            $countRates++;
          }
        }


        if (!empty($form)) {
          $more = ($countRates > 3 ? '<li class="sv_list-group-item sv_m-0 sv_text-center sv_text-dark sv_bg-light" onclick="shipvistaToggleViewMoreList()">
        <a href="javascript:void(0)" class="sv_text-dark "><small id="_shipvistaMoreList">MORE <i class="fa fa-chevron-down"></i></small></a>
        </li>' : '');

          $form = '
        <input type="hidden" value="' . $this->orderId . '" name="shipvistaLabel_order_id" id="shipvistaLabel_order_id">
        <input type="hidden" value="0" name="shipvistaLabel_get_label" id="shipvistaLabel_get_label">
        <input type="hidden" value="0" name="shipvista_shipping_carrier" id="shipvista_shipping_carrier">
        <input type="hidden" value="0" name="shipvista_shipping_options" id="shipvista_shipping_options">
        <ul class="sv_list woocommerce-shipping-methods" id="_shpvistaShippingList">
          ' . $form . '
          ' . $more . '
        </ul>
      <div class="wide sv_border-top pt-3">
      <button type="button" onclick="shipvistaSubmitlabelCreate()" class="sv_btn sv_btn-success sv_btn-sm sv_btn-block" >Get Label</button>
      </div>
      ';
        }
      }
      if (empty($form)) {
        $form = '
      <div class="sv_alert sv_alert-bg sv_alert-warning"> Could not find any shipping rate for this order.</div>
      ';
      }
    }
    return $form;
  }

  public function checkToken()
  {
    // $expires = $this->get_option('shipvista_token_expires');
    $expires = $this->get_option('shipvista_token_request_date');
    $today = strtotime('now');
    if (!empty($expires)) {
      $dateExpires =  (int) $expires;
      $diff = round(abs($today - $dateExpires) / 60);
      if($diff < 0){
        $diff = 31;
      }
    } else {

      $this->SLSR_pluginLogs('Authentication', 'Token expires not found');
      $diff = 31;
    }
    // check days
    if ($diff >= 25) {
      // refresh token
      $user = $this->get_option('shipvista_user_name');
      $pass = $this->get_option('shipvista_user_pass');
      $refreshObject = $this->shipvistaApi('Login', ['user_id' => $user, 'password' => $pass], 'POST');

      if (array_key_exists('user_id', $refreshObject)) {
        $this->update_option('api_status_ok', 'yes');
        $this->update_option('shipvista_api_token', $refreshObject['access_token']['tokenString']);
        $this->update_option('shipvista_refresh_token', $refreshObject['refresh_token']['tokenString']);
        $this->update_option('shipvista_token_expires', $refreshObject['access_token']['expireAt']);
        $this->update_option('shipvista_token_request_date', $today);
        $this->update_option('shipvista_plugin_errors', '');
        $this->SLSR_pluginLogs('Authentication', 'Token refreshed successfully, after ' . " $diff minutes");
      } else { // could not refresh the token there was an error
        $this->update_option('api_status_ok', 'no');
        $this->SLSR_pluginLogs('Authentication', 'Could not refresh your access token on shipvista.com');
      }

      // refresh token
    } 
  }

  public function SLSR_pluginLogs($title,  ?string $error)
  {
    // check to see if logging is turned on
    if ($this->logStatus) {
      if (strlen($error) > 0) {
        $errorFile = fopen(SHIPVISTA__PLUGIN_DIR . "assets/logs/" . strtolower($title) . "_logs.txt", "a+") or die(SHIPVISTA__PLUGIN_DIR . "assets/logs/" . strtolower($title) . "_logs.txt" . " | Unable to open file!");
        $txt = date('Y-m-d h:i:s') . ": $error, \n";
        fwrite($errorFile, $txt);
        fclose($errorFile);
        if (in_array($title, $this->errorLogKeys)) {
          $errorList = @json_decode($this->get_option('shipvista_plugin_errors'), true) ?? [];
          $errorList[$title] = $error . ' > Time ' . date('Y-m-d h:i:s');
          $dbList = json_encode($errorList);
          $this->update_option('shipvista_plugin_errors', $dbList);
        }
      }
    }
  }

  /**
   * @return array $rateList
   */
  public function getCustomRates(int $isFallBack = 1)
  {
    global $woocommerce;
    $cartTotal = preg_replace("/&#?[a-z0-9]+;/i", '', $woocommerce->cart->get_cart_total());
    $cartTotal = (float) preg_replace('#[^0-9\.]#', '', str_replace(',', '.', $cartTotal));
    $customRates = $this->get_option('shipvista_custom_shipping_method') ?: '[]';
    $customRates = json_decode($customRates, true);
    $ratesList = [];
    // loop through rates
    foreach ($customRates as $key => $rate) {
      switch ($rate['type']) {
        case 'flat':
          if (isset($rate['fallback']) && ($rate['fallback'] == $isFallBack || $rate['fallback']  == 0)) {
            $ratesList[] = [
              'id' => "shipvista_custom_flat_$key",
              'label' => $rate['displayName'],
              'cost' => (float)$rate['cost'],
              'meta_data' => [
                'transit' => $rate['deliveryDays'],
                'free' => false,
                'is_default' => false,
                'attribute' => '',
                'carrier' => 'Shipvista Custom Rate'
              ]
            ];
          }
          break;
        case 'free':
          $list = [];
          if ($rate['requires'] == 'minimum_order_amount') {
            if ($cartTotal >= $rate['cost']) {
              $list = [
                'label' => $rate['displayName'],
                'cost' => 0,
                'meta_data' => [
                  'transit' => $rate['deliveryDays'],
                  'free' => true,
                  'rate' => 0,
                  'is_default' => false,
                  'attribute' => '',
                  'carrier' => 'Shipvista Custom Rate'
                ]
              ];
            }
          }
          if ($list) {
            $list['id'] = 'shipvista_custom_free_' . $key;
          }
          $ratesList[] = $list;
          break;
      }
    }
    return $ratesList;
  }

  /**
   * Get plugin default rates
   */
  public function getDefaultRates($missingAddress = false)
  {
    $defaultRateOn = $this->get_option('shipvista_fallback_rate_on');
    $shippingPrice = $this->get_option('shipvista_fallback_rate') ?: 50;
    if ($defaultRateOn == 'per_unit_quantity' && array_key_exists('list', $this->shippingList) && is_array($this->shippingList['list']) && count($this->shippingList['list']) > 0) {
      // get total quantity
      $totalQuantity = $this->shippingList['totalQuantity'];
      $shippingPrice *= $totalQuantity;
      // get total quantity
    } elseif ($defaultRateOn == 'per_cart_item' && array_key_exists('list', $this->shippingList)  && is_array($this->shippingList['list']) && count($this->shippingList['list']) > 0) {
      $totaltems = count($this->shippingList['list']);
      $shippingPrice *= $totaltems;
    }
    $title = 'Flat Rate';
    $rateList = [];
    if ($missingAddress != false) {
      $title = 'Address: Enter a valid postal code to get shipping cost.';
      $shippingPrice = '';
    } else {
      $rateList[] = [
        'label' => $title,
        'cost' => $shippingPrice,
        'meta_data' => [
          'is_default' => true
        ]
      ];
    }

    // call to get custom rates

    $custom = $missingAddress === 0 ? 0 : 1;
    $customRates = $this->getCustomRates($custom);
    $rateList = [...$rateList, ...$customRates];
    return $rateList;
  }



  /**
   * get Active carrier rated
   */
  public function getActiveCarrierMethods()
  {
    $availableMethods = [];
    foreach ($this->registeredCarriers as $carrier) {
      if ($this->get_option($carrier . "_enabled") == true) {

        $carrierInputName = $carrier;
        $carrierMethods = (array) json_decode($this->get_option($carrierInputName));
        $activeCarrier = str_replace('_', ' ', strtolower($carrier));
        $availableMethods[$activeCarrier] = $carrierMethods;
        // foreach ($carrierMethods as $key => $value) {
        //   if ($value->checked == true) {
        //     $availableMethods[$activeCarrier][] = $value->name;
        //   }
        // }
      }
    }
    return $availableMethods;
  }

  /**
   * Get apiHeaders to send
   */
  public function SLSR_getApiHeaders()
  {
    // 'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
    // check if the token is expired and refresh
    // $this->checkToken();
    $headers = [
      'Content-Type' => 'application/json',
      'Accept' => 'application/json',
      'Connection' => 'Keep-Alive',
      'Authorization' =>  'Bearer ' . $this->get_option('shipvista_api_token')
    ];
    return $headers;
  }

  /**
   * Php post to call api function
   */

  public function shipvistaApi(string $endPoint, $object = [], string $type = 'POST')
  {
    $this->apiHttpErrorCode = '';
    $url = $this->baseApiUrl . rtrim(ltrim($endPoint, '/'), '/');

    $header = $this->SLSR_getApiHeaders();
    $response =  $this->SLSR_cUrlGetData($url, $object, $header, $type);
    return  (array) $response ?? false;
  }



  function SLSR_cUrlGetData($url, $post_fields = null, $headers = [],  string $type = 'POST')
  {
    $body = [
      'method' => $type,
      'body' => (is_array($post_fields) ? json_encode($post_fields) : []),
      'headers' => $headers,
      'sslverify' => 1
    ];
    $result = wp_remote_request($url, $body);

    $this->SLSR_pluginLogs('API_request', json_encode(['HEADER' => $headers, 'POST' => $post_fields]));
    if (is_wp_error($result)) {
      $msg = $result->get_error_message();
      $this->SLSR_pluginLogs('API_ERR', $msg);
      return false;
    }

    $responseBody = json_decode($result['body'], true);
    $this->SLSR_pluginLogs('API_OK', $url);
    $this->SLSR_pluginLogs('API_response', json_encode($responseBody));

    return (array) $responseBody;
  }



  // set countries and their currencies
  public $countryCurrencies = array(
    'AF' => 'AFN', 'AL' => 'ALL', 'DZ' => 'DZD', 'AS' => 'USD', 'AD' => 'EUR', 'AO' => 'AOA', 'AI' => 'XCD', 'AQ' => 'XCD', 'AG' => 'XCD', 'AR' => 'ARS', 'AM' => 'AMD', 'AW' => 'AWG', 'AU' => 'AUD', 'AT' => 'EUR', 'AZ' => 'AZN', 'BS' => 'BSD', 'BH' => 'BHD', 'BD' => 'BDT', 'BB' => 'BBD', 'BY' => 'BYR', 'BE' => 'EUR', 'BZ' => 'BZD', 'BJ' => 'XOF', 'BM' => 'BMD', 'BT' => 'BTN', 'BO' => 'BOB', 'BA' => 'BAM', 'BW' => 'BWP', 'BV' => 'NOK', 'BR' => 'BRL', 'IO' => 'USD', 'BN' => 'BND', 'BG' => 'BGN', 'BF' => 'XOF', 'BI' => 'BIF', 'KH' => 'KHR', 'CM' => 'XAF', 'CA' => 'CAD', 'CV' => 'CVE', 'KY' => 'KYD', 'CF' => 'XAF', 'TD' => 'XAF', 'CL' => 'CLP', 'CN' => 'CNY', 'HK' => 'HKD', 'CX' => 'AUD', 'CC' => 'AUD', 'CO' => 'COP', 'KM' => 'KMF', 'CG' => 'XAF', 'CD' => 'CDF', 'CK' => 'NZD', 'CR' => 'CRC', 'HR' => 'HRK', 'CU' => 'CUP', 'CY' => 'EUR', 'CZ' => 'CZK', 'DK' => 'DKK', 'DJ' => 'DJF', 'DM' => 'XCD', 'DO' => 'DOP', 'EC' => 'ECS', 'EG' => 'EGP', 'SV' => 'SVC', 'GQ' => 'XAF', 'ER' => 'ERN', 'EE' => 'EUR', 'ET' => 'ETB', 'FK' => 'FKP', 'FO' => 'DKK', 'FJ' => 'FJD', 'FI' => 'EUR', 'FR' => 'EUR', 'GF' => 'EUR', 'TF' => 'EUR', 'GA' => 'XAF', 'GM' => 'GMD', 'GE' => 'GEL', 'DE' => 'EUR', 'GH' => 'GHS', 'GI' => 'GIP', 'GR' => 'EUR', 'GL' => 'DKK', 'GD' => 'XCD', 'GP' => 'EUR', 'GU' => 'USD', 'GT' => 'QTQ', 'GG' => 'GGP', 'GN' => 'GNF', 'GW' => 'GWP', 'GY' => 'GYD', 'HT' => 'HTG', 'HM' => 'AUD', 'HN' => 'HNL', 'HU' => 'HUF', 'IS' => 'ISK', 'IN' => 'INR', 'ID' => 'IDR', 'IR' => 'IRR', 'IQ' => 'IQD', 'IE' => 'EUR', 'IM' => 'GBP', 'IL' => 'ILS', 'IT' => 'EUR', 'JM' => 'JMD', 'JP' => 'JPY', 'JE' => 'GBP', 'JO' => 'JOD', 'KZ' => 'KZT', 'KE' => 'KES', 'KI' => 'AUD', 'KP' => 'KPW', 'KR' => 'KRW', 'KW' => 'KWD', 'KG' => 'KGS', 'LA' => 'LAK', 'LV' => 'EUR', 'LB' => 'LBP', 'LS' => 'LSL', 'LR' => 'LRD', 'LY' => 'LYD', 'LI' => 'CHF', 'LT' => 'EUR', 'LU' => 'EUR', 'MK' => 'MKD', 'MG' => 'MGF', 'MW' => 'MWK', 'MY' => 'MYR', 'MV' => 'MVR', 'ML' => 'XOF', 'MT' => 'EUR', 'MH' => 'USD', 'MQ' => 'EUR', 'MR' => 'MRO', 'MU' => 'MUR', 'YT' => 'EUR', 'MX' => 'MXN', 'FM' => 'USD', 'MD' => 'MDL', 'MC' => 'EUR', 'MN' => 'MNT', 'ME' => 'EUR', 'MS' => 'XCD', 'MA' => 'MAD', 'MZ' => 'MZN', 'MM' => 'MMK', 'NA' => 'NAD', 'NR' => 'AUD', 'NP' => 'NPR', 'NL' => 'EUR', 'AN' => 'ANG', 'NC' => 'XPF', 'NZ' => 'NZD', 'NI' => 'NIO', 'NE' => 'XOF', 'NG' => 'NGN', 'NU' => 'NZD', 'NF' => 'AUD', 'MP' => 'USD', 'NO' => 'NOK', 'OM' => 'OMR', 'PK' => 'PKR', 'PW' => 'USD', 'PA' => 'PAB', 'PG' => 'PGK', 'PY' => 'PYG', 'PE' => 'PEN', 'PH' => 'PHP', 'PN' => 'NZD', 'PL' => 'PLN', 'PT' => 'EUR', 'PR' => 'USD', 'QA' => 'QAR', 'RE' => 'EUR', 'RO' => 'RON', 'RU' => 'RUB', 'RW' => 'RWF', 'SH' => 'SHP', 'KN' => 'XCD', 'LC' => 'XCD', 'PM' => 'EUR', 'VC' => 'XCD', 'WS' => 'WST', 'SM' => 'EUR', 'ST' => 'STD', 'SA' => 'SAR', 'SN' => 'XOF', 'RS' => 'RSD', 'SC' => 'SCR', 'SL' => 'SLL', 'SG' => 'SGD', 'SK' => 'EUR', 'SI' => 'EUR', 'SB' => 'SBD', 'SO' => 'SOS', 'ZA' => 'ZAR', 'GS' => 'GBP', 'SS' => 'SSP', 'ES' => 'EUR', 'LK' => 'LKR', 'SD' => 'SDG', 'SR' => 'SRD', 'SJ' => 'NOK', 'SZ' => 'SZL', 'SE' => 'SEK', 'CH' => 'CHF', 'SY' => 'SYP', 'TW' => 'TWD', 'TJ' => 'TJS', 'TZ' => 'TZS', 'TH' => 'THB', 'TG' => 'XOF', 'TK' => 'NZD', 'TO' => 'TOP', 'TT' => 'TTD', 'TN' => 'TND', 'TR' => 'TRY', 'TM' => 'TMT', 'TC' => 'USD', 'TV' => 'AUD', 'UG' => 'UGX', 'UA' => 'UAH', 'AE' => 'AED', 'GB' => 'GBP', 'US' => 'USD', 'UM' => 'USD', 'UY' => 'UYU', 'UZ' => 'UZS', 'VU' => 'VUV', 'VE' => 'VEF', 'VN' => 'VND', 'VI' => 'USD', 'WF' => 'XPF', 'EH' => 'MAD', 'YE' => 'YER', 'ZM' => 'ZMW', 'ZW' => 'ZWD',
  );
}
