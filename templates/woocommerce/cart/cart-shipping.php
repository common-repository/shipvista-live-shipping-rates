<?php

/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

$formatted_destination    = isset($package['destination']['country']) && isset($package['destination']['postcode']) ? $package['destination']['country'] .', '.$package['destination']['city'].' ' . $package['destination']['postcode'] : (isset($formatted_destination) ? $formatted_destination : WC()->countries->get_formatted_address($package['destination'], ', '));
$has_calculated_shipping  = !empty($has_calculated_shipping);
$show_shipping_calculator = !empty($show_shipping_calculator);

$calculator_text          = '';

$carriersLogo = [
    'canadapost' => [
      'name' => 'Canada Post',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/canada_post.jpg'
    ],
    'ups' => [
      'name' => 'UPS',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/ups_logo.png'
    ],
    'canpar' => [
      'name' => 'CANPAR',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/canpar_logo.png'
    ],
    'usps' => [
      'name' => 'USPS',
      'image' => SHIPVISTA__PLUGIN_URL . 'assets/img/usps_logo.png'
    ]
    ];

function addDays($timestamp, $days, $skipdays = array("Saturday", "Sunday"), $skipdates = []) {
    // $skipdays: array (Monday-Sunday) eg. array("Saturday","Sunday")
    // $skipdates: array (YYYY-mm-dd) eg. array("2012-05-02","2015-08-01");
   //timestamp is strtotime of ur $startDate
    $i = 1;

    while ($days >= $i) {
        $timestamp = strtotime("+1 day", $timestamp);
        if ( (in_array(date("l", $timestamp), $skipdays)) || (in_array(date("Y-m-d", $timestamp), $skipdates)) )
        {
            $days++;
        }
        $i++;
    }

    return $timestamp;
    //return date("m/d/Y",$timestamp);
}

?>
<tr class="woocommerce-shipping-totals shipping" >
    <!-- <th colspan="2"><?php echo wp_kses_post($package_name); ?></th>
    <td colspan="2" data-title="<?php echo esc_attr($package_name); ?>"> -->
    <!-- <tr> -->
    <td colspan="2" style="text-align: left !important;">
        <?php if ($available_methods) { ?>
            <?php $list = '';
            $in = 0;
            foreach ($available_methods as $method) : $in++;
                $hideListClass = '';
                if ($in > 3 && !checked($method->id, $chosen_method, false)) {
                    $hideListClass = ' sv_d-none shipvista_list_hide ';
                }

                $meta = $method->get_meta_data();
                $badge = '';
                $transit = '';
                if (isset($meta['attribute'])) {
                    switch ($meta['attribute']) {
                        case 'Fastest':
                            $badge = '<small class="sv_badge sv_badge-warning  sv_text-white">Fastest</small><br>';
                            break;
                        case 'Cheapest':
                            $badge = '<small class="sv_badge sv_badge-success  sv_text-white">Cheapest</small><br>';
                            break;
                        case 'Recommended':
                            $badge = '<small class="sv_badge sv_badge-primary  sv_text-white">Recommended</small><br>';
                            break;
                        default:
                            break;
                    }
                    if ($meta['transit'] > 0) {
                        $time = addDays(strtotime('now'), $meta['transit']);//' - ' . $meta['transit'] . ' day' . ($meta['transit'] > 0 ? 's' : '');
                        $transit = 'Get it '.date('l, M d', addDays(strtotime('now'), $meta['transit'])) . '';//' - ' . $meta['transit'] . ' day' . ($meta['transit'] > 0 ? 's' : '');
                    }
                }

                $listPre = '
                <li class="sv_list-group-item sv_text-left sv_m-0 ' . $hideListClass . '" style="padding-left: 5px;">
                    <div class="sv_d-flex">
                        <div class="sv_px-2 sv_align-self-center">

                            ' .

                    ((1 < count($available_methods)) ?
                        ('<input type="radio" name="shipping_method[' . $index . ']" data-index="' . $index . '" id="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '" value="' . esc_attr($method->id) . '" ' . checked($method->id, $chosen_method, false) . ' class="shipping_method sv_radio" ' . false . ' />') // WPCS: XSS ok.
                        : ('<input type="hidden" name="shipping_method[' . $index . ']" data-index="' . $index . '" id="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '" value="' . esc_attr($method->id) . '" ' . checked($method->id, $chosen_method, false) . ' class="shipping_method sv_radio" />') // WPCS: XSS ok.
                    )
                    . '
                        </div>

                        <div class="col-8 sv_align-self-center">
                            ' .$badge  . 
                    ('<label for="shipping_method_' . $index . '_' . esc_attr(sanitize_title($method->id)) . '" style="line-height:15px;width:100%;" class="font-14">' . ( isset($meta['carrier']) && array_key_exists($meta['carrier'], $carriersLogo) ? '<img src="'.$carriersLogo[$meta['carrier']]['image'].'" title="'.$carriersLogo[$meta['carrier']]['name'].'" style="height:30px;width:30px;float:left;margin-right: 5px;">'.$method->get_label() . '<small class=""> <br>'.$transit.'</small>' :   $method->get_label() . '<br><small>'.$transit.'</small>') . '</label>') // WPCS: XSS ok.
                    . '

                        </div>
                        <div class="sv_align-self-center col-4 font-14">
                            <b>

                                ' .  ($method->get_cost() > 0 ? get_woocommerce_currency_symbol() . round($method->get_cost(), 2) : 'Free') . '
                            </b>
                        </div>
                        
                        </div>
                        </li>';

                if (checked($method->id, $chosen_method, false)) {
                    $list = $listPre . $list;
                } else {
                    $list .= $listPre;
                }
                do_action('woocommerce_after_shipping_rate', $method, $index);
            endforeach; ?>

            <?php if (is_cart()) { ?>
                <h4 class="sv_shipping-title" style="text-align: left !important;">Delivery Options</h4>

            <?php } elseif (is_checkout()) { ?>
                <h4 class="sv_shipping-title" style="text-align: left !important;">Delivery Options</h4>

            <?php } ?>

            <div id="_shipvistaListingInView"></div>
            <ul id="_shpvistaShippingList" class="sv_list-group sv_mb-3" style="padding-left: 5px;">
                <?php echo $list; ?>

                <?php if ($in > 3) { ?>
                    <li class="sv_list-group-item sv_m-0 sv_text-center sv_text-dark sv_bg-light" onclick="shipvistaToggleViewMoreList()">
                        <a href="javascript:void(0)" class="sv_text-dark "><small id="_shipvistaMoreList">MORE <i class="fa fa-chevron-down"></i></small></a>
                    </li>
                <?php } ?>


            </ul>

            <div class="sv_text-right sv_w-100 "><small style="color:#4B4B4B"><i>Live rates powered by <b>shipvista.com</b></i></small></div>


            <p class="woocommerce-shipping-destination" style="text-align: left;">
                <?php
                if (is_cart()) {
                    if ($formatted_destination) {
                        // Translators: $s shipping destination.
                ?>
                        <a class="sv_float-right sv_btn sv_btn-text shipping-calculator-button sv_text-danger sv_m-0" style="margin-top:-8px !important" onclick="toggleUpdateForm()"> <small>Update</small></a>
                <?php
                        printf(esc_html__('Shipping to %s.', 'woocommerce') . ' ', '<br><small><strong>' . esc_html($formatted_destination) . '</strong></small>');
                        // $calculator_text ='';// esc_html__('<span class="d-none">Change address</span>', 'woocommerce');
                    } else {
                        echo wp_kses_post(apply_filters('woocommerce_shipping_estimate_html', __('Shipping options will be updated during checkout.', 'woocommerce')));
                    }
                }
                ?>
            </p>

        <?php
        } elseif (!$has_calculated_shipping || !$formatted_destination) { ?>
            <div class="sv_d-flex mb-2">
                <div class="sv_flex-fill align-self-center" style="text-align: left !important;">
                    <h4 class="mb-0 font-14">Delivery Options</h4>
                </div>
                <?php if(is_cart()){ ?><div class=" pl-2"><a id="sv_calculateBtnToggle" onclick="toggleUpdateForm()" class="sv_btn sv_btn-sm sv_btn-danger sv_text-white"><small>Calculate Now</small></a></div> <?php } ?>
            </div>

        <?php
            if (is_cart() && 'no' === get_option('woocommerce_enable_shipping_calc')) {
                echo wp_kses_post(apply_filters('woocommerce_shipping_not_enabled_on_cart_html sv_text-left', __('<div class="text-warning" style="text-align: left !important;"><span class="dashicons dashicons-no"></span> Shipping costs are calculated during checkout.</div>', 'woocommerce')));
            } else {

                echo wp_kses_post(apply_filters('woocommerce_shipping_may_be_available_html sv_text-left', __('<div class="text-warning" style="text-align: left !important;"><span class="dashicons dashicons-no"></span> Enter your address to view shipping options.</div>', 'woocommerce')));
            }
        } elseif (!is_cart()) {
            echo wp_kses_post(apply_filters('woocommerce_no_shipping_available_html sv_text-left', __('<div class="text-warning" style="text-align: left !important;"><span class="dashicons dashicons-no"></span> There are no shipping options available. Please ensure that your address has been entered correctly, or contact us if you need any help.</div>', 'woocommerce')));
        } else {
            // Translators: $s shipping destination.
            echo wp_kses_post(apply_filters('woocommerce_cart_no_shipping_available_html sv_text-left', sprintf(esc_html__('No shipping options were found for %s.', 'woocommerce') . ' ', '<strong><span class="text-warning"><span class="dashicons dashicons-no"></span> ' . esc_html($formatted_destination) . '<span></strong>')));
            $calculator_text = esc_html__('Enter a valid postal code to get shipping cost', 'woocommerce');
        }
        ?>

        <?php if ($show_package_details) : ?>
            <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
        <?php endif; ?>

        <?php if ($show_shipping_calculator && is_cart()) :  ?>
        <?php if(!empty($calculator_text)): ?>
        <div class="py-2"> <p class="sv_mb-2"><?php echo $calculator_text ?> </p><a id="sv_calculateBtnToggle" onclick="toggleCartShippingFields()" class="sv_btn sv_btn-sm sv_btn-danger sv_text-white"><small>Update Address</small></a></div>
        <?php endif; ?>
            <?php woocommerce_shipping_calculator($calculator_text); ?>
        <?php endif; ?>
    </td> 
</tr>