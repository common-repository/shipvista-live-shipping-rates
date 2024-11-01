<div class="sv_container">
    <div class="sv_pt-3">
        <h4 class="sv_mb-3"><i class="fa fa-cog"></i> Settings</h4>
        <!-- set header -->

        <nav class="sv_navbar  sv_p-0 sv_navbar-expand-lg sv_navbar-light sv_bg-light sv_text-dark sv_m-0">

            <div class="sv_m-0 " id="sv_navbarNav">
                <ul class="sv_d-flex sv_m-0 sv_p-0 sv_flex-wrap">
                    <li class="sv_nav-item sv_border-right sv_m-0  <?php echo  esc_attr(($this->settingTabs == 'basic' || $this->settingTabs == '')  ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'basic' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=basic') ?>">Basic</a>
                    </li>
                    <li class="sv_nav-item sv_border-right sv_m-0  <?php echo ($this->settingTabs == 'shipper' ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'shipper' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=shipper') ?>">Warehouse Locations</a>
                    </li>
                    <li class="sv_nav-item sv_border-right  sv_m-0 <?php echo ($this->settingTabs == 'dimension' ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'dimension' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=dimension') ?>">Dimension </a>
                    </li>
                    <li class="sv_nav-item sv_border-right sv_m-0 <?php echo ($this->settingTabs == 'restrict' ? 'sv_active sv_bg-dark sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'restrict' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=restrict') ?>">Restrictions </a>
                    </li>
                    <li class="sv_nav-item sv_border-right sv_m-0 <?php echo ($this->settingTabs == 'custom' ? 'sv_active sv_bg-dark sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'custom' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=custom') ?>">Custom Shipping </a>
                    </li>

                    <li class="sv_nav-item  sv_m-0 <?php echo  esc_attr(($this->settingTabs == 'apis' ? 'sv_active sv_bg-dark  sv_text-white' : '')) ?>">
                        <a class="sv_nav-link <?php echo  esc_attr(($this->settingTabs == 'apis' ? 'sv_text-white' : 'sv_text-dark')) ?>" href="<?php echo  esc_attr($this->pageLink . '&wcs_page=settings&wcs_setting=apis') ?>">Third Party APIs </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- end set headers -->


    <div class=" sv_table-responsive sv_mt-0">

        <table class="sv_table sv_border-0">
            <thead class="sv_border-0 shipping-header">
                <tr class="sv_bg-dark sv_border-0 sv_text-white">
                    <th>#</th>
                    <th>Values</th>
                </tr>
            </thead>
            <?php if ($this->settingTabs == 'feedback') { ?>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <h4>FeedBack</h4>
                            <p class="sv_mb-3">
                                Having errors using this plugin? We would love to get your feedback on this plugin and how we can make it better for the community.
                            </p>

                            <!-- thank you note -->

                            <div class="mb-3">
                                <textarea class="sv_form-control" style="min-height: 80px;" rows="20" name="shipvista_feedback" id="shipvista_feedback" minlength="3" placeholder="Enter feedback"></textarea>
                            </div>

                            <div calss="sv_mb-3">
                                <button type="button" class="sv_btn sv_btn-info" onclick="sv_WooSave()"> Submit Feedback</button>
                            </div>

                        </td>
                    </tr>

                <tbody>
                <?php } elseif ($this->settingTabs == 'restrict') { ?>
                <tbody>

                    <tr>
                        <td colspan="2">
                            <h4>Restrict Postal Codes/Zip Codes</h4>
                            <p>Use this option to set restriction to locations where shipping discounts will not apply.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Locations <br>
                            <p>Sample Format: Country_Code_1: POSTCODES_1,POSTCODE_2, etc.. | Country_Code_2 ....</p>
                            <small>Separate each country (only 2 characters) with a pipe( | ),<br> Separate country code with restricted postcodes with a colon( : ), <br>Separate each restricted postal code with a comma( , ).</small> <br>
                            <small><b>All postal codes beginning with that set in here will be restricted. E.g. You can put one or more letters to restrict all postal codes beginning with that letter. </b></small>
                        </td>
                        <td>
                            <textarea class="sv_form-control" class="custom-control-input" id="<?php echo  ($this->fieldPrepend) ?>shipvista_restricted_locations" cols="20" rows="15" style="min-width: 300px;min-height:200px" placeholder="Enter locations" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_restricted_locations"><?php echo  esc_attr($this->get_option('shipvista_restricted_locations')); ?></textarea>
                        </td>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" onclick="sv_WooSave()" class="sv_btn sv_btn-primary"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>

            <?php } elseif ($this->settingTabs == 'custom') { ?>
                <style>
                    .shipping-header {
                        display: none !important;
                    }
                </style>

                <!-- shipper setting -->
                <tbody>
                    <tr class="">
                        <td colspan="2">
                            <div class="float-right" id="action-button">
                                <button class="btn btn-dark btn-sm d-none" type="button" onclick="toggleAddressForm(true)">Add Shipping method</button>
                            </div>
                            <h4>Custom Shipping</h4>
                            <p>Setup custom shipping method to use in addition to live rates from. By default all flat rates are only applied as fallback rate.</p>
                        </td>
                    </tr>


                    <tr class="_form_element">
                        <td colspan="2" class="p-0">
                            <div class="container mb-3 p-0 pt-2">
                                <div class="d-flex flex-wrap">
                                    <div class="col-12 col-md-6 m-0 ">
                                        <div class="mb-3">
                                            <h6>Shipping Method</h6>
                                        </div>
                                        <div class="py-1 mb-3">
                                            <select name="shipping_method" style="max-width: 100% !important;" class="custom-select" id="shipping_method">
                                                <option value="_flat">Flat Rate</option>
                                                <option value="_free">Free Shipping</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="">Display Name</label>
                                            <input type="text" id="_display_name" value="" class="form-control" placeholder="Enter shipping display name.">
                                        </div>

                                        <!-- flat rate options -->
                                        <div class="_shippingMethods" id="_flat">
                                            <div class="mb-3">

                                                <div class="custom-control custom-switch float-right">
                                                    <input type="checkbox" checked class="custom-control-input" id="_only_fallback">
                                                    <label class="custom-control-label" for="_only_fallback"></label>
                                                </div>
                                                Only Fallback <?php echo wc_help_tip("Disable this option to allow flat rates to display with rates obtain from shipping carriers."); ?>
                                            </div>
                                            <div class="mb-3">
                                                <label for="">Cost</label>
                                                <input type="number" id="_shipping_method_cost" class="form-control" placeholder="Enter cost">
                                            </div>
                                        </div>
                                        <!-- flat rate options -->

                                        <!-- free shipping method -->
                                        <div class="_shippingMethods sv_d-none" id="_free">
                                            <div class="py-1 mb-3">
                                                <label value="Free">Free Shipping requires</label>

                                                <select name="free_shipping_require" style="max-width: 100% !important;" class="custom-select" id="free_shipping_require">
                                                    <option value="minimum_order_amount">Minimum order amount</option>
                                                </select>
                                            </div>


                                            <div class="mb-3">
                                                <label for="">Cost</label>
                                                <input type="number" id="_free_shipping_method_cost" min="1" class="form-control" placeholder="Enter cost">
                                            </div>
                                        </div>
                                        <!-- free shipping method -->

                                        <div class="mb-3">
                                            <label for="">Delivery Days</label>
                                            <input type="number" id="_shipping_method_days" min="1" class="form-control" placeholder="Enter expected delivery days">
                                        </div>

                                        <div class="mb-3">
                                            <button class="btn btn-block btn-dark" type="button" onclick="addShippingMethod()">Add Shipping Method</button>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 border-left">
                                        <div class="mb-3">
                                            <h6>
                                                Custom Rates
                                            </h6>
                                        </div>

                                        <div class="py-3 d-flex flex-wrap">
                                            <?php $methods = $this->get_option('shipvista_custom_shipping_method') ?: '[]';
                                            $content = json_decode($methods, true);
                                            if (count($content) > 0) {

                                                foreach ($content as $key => $value) { ?>
                                                    <div class="col-12 py-2 <?php echo $key != 0 ? 'border-top' : ''; ?>">
                                                        <div class="row">
                                                            <div class="col-4">
                                                                <?php echo $value['displayName'] ?>
                                                            </div>
                                                            <div class="col-8">
                                                                <div class="float-right"><i class="dashicons dashicons-trash" style="cursor:pointer" onclick="removeShippingMethod(<?php echo $key; ?>)"></i></div>
                                                                Cost: <?php echo $value['cost'] ?> <br>
                                                                Days: <?php echo $value['deliveryDays'] ?>
                                                                <?php if (isset($value['requires'])) {
                                                                    echo '<br>' . "Requires: $value[requires]";
                                                                } ?>
                                                                <?php if (isset($value['type']) && $value['type'] == 'flat') {
                                                                    echo '<br>' . "Fallback Only: " . ($value['fallback'] ? "Yes" : "No");
                                                                } ?>
                                                            </div>
                                                        </div>
                                                    </div>


                                                <?php } ?>

                                            <?php } else { ?>
                                                <div class="flex-fill">
                                                    <div class="jumbotron">
                                                        <p class="lead">No custom shipping method available</p>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>

                                    </div>


                                    <input type="hidden" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_custom_shipping_method" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_custom_shipping_method" value='<?php echo $this->get_option('shipvista_custom_shipping_method') ?>' />
                                </div>
                            </div>

                        </td>
                    </tr>


                </tbody>

            <?php } elseif ($this->settingTabs == 'shipper') { ?>
                <style>
                    .shipping-header {
                        display: none !important;
                    }
                </style>



                <!-- shipper setting -->
                <tbody>
                    <tr class="">
                        <td colspan="2">
                            <div class="float-right" id="action-button">
                                <button class="btn btn-primary" type="button" onclick="toggleAddressForm(true)">Add Location</button>
                            </div>
                            <h4>Warehouses</h4>
                            <p>Use this option to set your ship from locations. You store address will be used as default warehouse location.</p>
                        </td>
                    </tr>

                    <tr class="_form_element d-none">
                        <td colspan="2" class="p-0">
                            <div class="container mb-3 p-0 pt-2">
                                <div class="d-flex flex-wrap">
                                    <div class="col-12 col-md-6 m-0 ">

                                        <div class="mb-3">
                                            <h5>Warehouse Location</h5>
                                        </div>

                                        <!-- set from location -->
                                        <div class="mb-3">
                                            <div class="sv_float-right"><?php echo wc_help_tip("Enter nick name for address."); ?> </div>
                                            <label for="">Nick Name</label>
                                            <input type="text" class="form-control" id="_nickname" name="_nickname" />
                                        </div>
                                        <!-- set from location -->

                                        <div class="mb-3">
                                            <?php
                                            global $woocommerce;
                                            $countries_obj   = new WC_Countries();
                                            $countries   = $countries_obj->__get('countries');
                                            // $current_cc = WC()->customer->get_shipping_country();
                                            // $current_r  = WC()->customer->get_shipping_state();
                                            // $states     = WC()->countries->get_states( $current_cc );
                                            // die(var_dump($countries_obj->get_shipping_countries(), 'ddddd'));

                                            ?>
                                            <div class="sv_float-right"> <?php echo wc_help_tip("Select a valid country."); ?> </div>
                                            <label for="">Country</label>
                                            <select name="from_country" id="from_country" class="form-control custom-select" style="max-width: 100% !important;">
                                                <option value=""> Select Country</option>
                                                <?php
                                                foreach ($countries_obj->get_shipping_countries() as $key => $value) {
                                                    echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3 sv_row">
                                            <div class="sv_col-12 sv_col-md-6">
                                            <div class="sv_float-right"> <?php echo wc_help_tip("Select a valid ship from state."); ?> </div>
                                            <label for="">State</label>
                                            <select name="from_state" disabled id="from_state" class="form-control custom-select" style="max-width: 100% !important;">
                                                <option>Selecte State</option>
                                            </select>
                                            </div>
                                            <div class="sv_col-12 sv_col-md-6">
                                            <div class="sv_float-right"> <?php echo wc_help_tip("Enter city."); ?> </div>
                                                <label for="">City</label>
                                                <input type="text" class="sv_form-control" id="from_city" name="from_city" />
                                            </div>
                                            <!-- <input type="text" minlength="2" maxlength="2" class="form-control" id="from_state" placeholder="State"> -->
                                        </div>

                                        <div class="mb-3">
                                            <div class="sv_float-right"><?php echo wc_help_tip("Select a valid zip/postal code."); ?></div>
                                            <label for="">Zip Code </label>
                                            <input type="text" minlength="4" class="form-control" id="from_zip_code" placeholder="Zip Code">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 border-left">
                                        <div class="mb-3">
                                            <h5>
                                                Delivery locations
                                            </h5>
                                        </div>
                                        <div class="mb-3">
                                            <label for="">Countries</label><br>
                                            <select name="to_country" multiple id="to_country" style="width: 100% !important;height: 35px;">
                                                <option value=""> Select Country</option>
                                                <?php
                                                foreach ($countries_obj->get_shipping_countries() as $key => $value) {
                                                    echo '<option value="' . esc_attr($key) . '">' . esc_html($value) . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <div class="sv_float-right"><?php echo wc_help_tip("Select states you will ship to using the from address. Leaving this option blank will mean shipping to the entire selected countries above."); ?></div>
                                            <label for="">States </label><br>
                                            <select name="to_state" multiple id="to_state" style="width: 100% !important;height: 35px;">
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <div class="sv_float-right"><?php echo wc_help_tip("Enter comma seperated postal or zip code, you'd ship to using the from address."); ?></div>
                                            <label for="">Zip Codes </label>
                                            <input type="text" class="form-control" id="to_zip_codes" placeholder="Delivery Zip Codes">
                                        </div>

                                    </div>


                                    <input type="hidden" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_address_book" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_address_book" value='<?php echo $this->get_option('shipvista_address_book') ?>' />
                                </div>
                            </div>

                        </td>
                    </tr>


                    <tr class="_form_element destination_list">
                        <td colspan="2">
                            <div class="container">
                                <div class="d-flex flex-wrap w-100">
                                    <?php
                                    $destinations = [];

                                    if ($this->settingTabs == 'shipper') {
                                        $settings = $this->get_option('shipvista_address_book');
                                        $destinations = $settings ? json_decode($settings, true) : [];
                                        // set default location
                                        array_unshift($destinations, [
                                            'nickname' => 'Default Ship from location',
                                            'from_address' => ['country' => $this->content['storeLocation']['countryCode'], 'state' => $this->content['storeLocation']['stateCode'], 'city' => $this->content['storeLocation']['city'], 'zip_code' => $this->content['storeLocation']['postalCode']],
                                            'to_address' => ['country' => '', 'state' => '', 'zip_code' => ''],
                                        ]);
                                        // die(var_dump($destinations));
                                    }
                                    if (count($destinations) > 0) {
                                        foreach ($destinations as $id => $destination) { ?>
                                            <div class="col-12 mb-3 border" id="address_<?php echo $id; ?>">
                                                <div class="">
                                                    <div class="card-body d-flex flex-wrap">
                                                        <div class="col-<?php echo ($id == 0 ? '12' : '4') ?> align-self-center mb-3">
                                                            <b><?php echo (isset($destination['nickname']) && strlen($destination['nickname']) > 0 ? $destination['nickname'] : 'Address ' . ($id )) ?></b>
                                                        </div>
                                                        <?php if ($id != 0) { ?>
                                                            <div class="col-8 align-self-center mb-3">
                                                                <div class="d-flex justify-content-end">
                                                                    <div class="px-2">
                                                                        <span class="" style="cursor: pointer;" data-content='<?php echo json_encode($destination); ?>' onclick="editLocation(<?php echo $id; ?>)" id="_address_<?php echo $id; ?>">Edit</span>
                                                                    </div>
                                                                    <div class="px-2">
                                                                        <span class="" style="cursor: pointer;" onclick="cloneLocation(<?php echo $id; ?>)">Clone</span>
                                                                    </div>
                                                                    <div class="px-2">
                                                                        <span class="sv_text-danger" style="cursor: pointer;" onclick="deleteLocation(<?php echo $id; ?>)">Delete</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>

                                                        <div class="col-12 col-md-5">
                                                            <div class="mb-2"><small>Warehouse Address</small></div>
                                                            <div class="mb-2">
                                                                <div class="d-flex">
                                                                    <div class="flex-fill">
                                                                        <small>Country</small> <br>
                                                                        <b><?php echo $destination['from_address']['country']; ?></b>
                                                                    </div>

                                                                    <div class="flex-fill">
                                                                        <small>State</small> <br>
                                                                        <b><?php echo $destination['from_address']['state']; ?></b>
                                                                    </div>

                                                                    
                                                                    <div class="flex-fill">
                                                                        <small>City</small> <br>
                                                                        <div style="max-width: 80px;text-overflow:ellipsis;" title="<?php echo $destination['from_address']['city']; ?>"><b><?php echo $destination['from_address']['city']; ?></b></div>
                                                                    </div>

                                                                    <div class="flex-fill">
                                                                        <small>Zip Code</small> <br>
                                                                        <b><?php echo $destination['from_address']['zip_code']; ?></b>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-7 border-left">
                                                            <div class="mb-2"><small>Delivery Locations</small></div>
                                                            <div class="mb-2">
                                                                <div class="d-flex">
                                                                    <div class="flex-fill">
                                                                        <small>Countries</small> <br>
                                                                        <span><?php echo is_array($destination['to_address']['country']) ? implode(', ', $destination['to_address']['country']) : ($destination['to_address']['country'] ? $destination['to_address']['country'] : '<small>ALL COUNTRIES</small>'); ?></span>
                                                                    </div>

                                                                    <div class="flex-fill" title="<?php echo str_replace(['"', '[', ']', '{', '}'], '', json_encode($destination['to_address']['state'])); ?>" style="width: 50px;
                                                                        white-space: nowrap;
                                                                        overflow: hidden;
                                                                        text-overflow: ellipsis;">
                                                                        <small>States</small> <br>
                                                                        <span><?php echo str_replace(['"', '[', ']', '{', '}'], '', json_encode($destination['to_address']['state'])) ?: '<small>ALL STATES</small>'; ?></span>
                                                                    </div>

                                                                    <div class="flex-fill" style="width: 50px;
                                                                        white-space: nowrap;
                                                                        overflow: hidden;
                                                                        text-overflow: ellipsis;">
                                                                        <small>Zip Codes</small> <br>
                                                                        <span><?php echo $destination['to_address']['zip_code'] ?: '<small>ALL ZIP CODES</small>'; ?></span>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php }
                                    } else { ?>
                                        <div class="col-12">
                                            <div class="jumbotron">
                                                <div class="py-5  text-center">
                                                    <h3 class="display">No address in address book</h3>
                                                    <p>You currently do not have any warehouse locations. Your default store address will be used to get rates. </p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </td>
                    </tr>

                </tbody>
                <tfoot class="_form_element addressButton d-none">
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" onclick="sv_saveAddressBook()" class="sv_btn sv_btn-primary"> Save
                                    Address</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>

                <script type="module">
                    var countryObject = <?php echo json_encode($countries_obj->get_shipping_countries()); ?>;
                    window.addEventListener('load', evt => {
                        // jQuery(function() {
                        var totalElements = [];
                        jQuery("#to_country").selectize({
                            maxItems: <?php echo count($countries_obj->get_shipping_countries()) ?>,
                            onChange: function(countries) {
                                changeToCountry(countries);
                            },
                            onItemRemove: function(stateCode) {
                                var $select = jQuery('#to_state').selectize();
                                var selectize = $select[0].selectize;
                                if (document.querySelector('#_country_' + stateCode).getAttribute('data-group-id')) {
                                    var id = document.querySelector('#_country_' + stateCode).getAttribute('data-group-id');
                                    // remove items
                                    console.log(totalElements);
                                    for (const [key, value] of Object.entries(totalElements)) {
                                        if (key == stateCode) {
                                            value.map(element => {
                                                if (element.groupLabel == id) {
                                                    selectize.removeItem(element.value, true);
                                                    selectize.removeOption(element.value, true)
                                                }
                                            })
                                        }
                                    }
                                    selectize.removeOptionGroup(id);
                                    selectize.refreshItems();
                                    selectize.refreshOptions();
                                }


                            },
                        });
                        // });

                        let countryList = [];

                        async function changeToCountry(countries) {

                            // var countries = jQuery('#to_country').val(); //e.target.value;

                            // check if a state was added or removed
                            let difference = countries.filter(x => !countryList.includes(x));

                            var listTotal = 0;
                            if (difference.length > 0) {
                                var optionList = [];
                                var optionGroup = [];

                                await difference.forEach(country => {
                                    // load country states
                                    jQuery.post(
                                        window.location, {
                                            'shipvista_post_request': 'getStates',
                                            country,
                                        },
                                        (response) => {
                                            if (response.status) {
                                                console.log(response)
                                                var countryOptions = `<optgroup label="States in ${countryObject[country]}" id="_countryStates_${country}">`;
                                                optionGroup.push({
                                                    value: countryObject[country],
                                                    label: countryObject[country],
                                                    id: country
                                                });
                                                Object.keys(response.states).forEach(stateCode => {
                                                    countryOptions += `<option value="${country}.${stateCode}">${response.states[stateCode]}</option>`;
                                                    optionList.push({
                                                        text: response.states[stateCode],
                                                        value: country + '.' + stateCode,
                                                        groupLabel: `${countryObject[country]}`
                                                    });

                                                });
                                                listTotal += Object.keys(response.states).length
                                                countryOptions += `</optgroup>`;

                                                countryList.push(country);
                                            } else {
                                                alertBar(response.message);
                                            }
                                        }).fail(e => {
                                        console.log(e);
                                        alertBar('Could not get ' + country + ' states');
                                    });

                                });
                                var timerSet = setInterval(() => {
                                    if (optionList.length > 0) {
                                        clearInterval(timerSet);
                                        var $select = jQuery('#to_state').selectize({
                                            plugins: ['remove_button'],
                                            persist: false,
                                            create: true,
                                            optgroupField: 'groupLabel',
                                            labelField: 'text',
                                            searchField: ['text'],
                                            optgroupLabelField: optionGroup.value,
                                            optgroups: optionGroup,
                                            render: {
                                                optgroup_header: function(data, escape) {
                                                    return '<div class="optgroup-header" id="_country_' + data[0].id + '" data-group-id="' + escape(data.value) + '"><b>States in ' + escape(data.value) + '</b></div>';
                                                }
                                            }
                                        });
                                        var selectize = $select[0].selectize;
                                        totalElements[optionGroup[0].id] = optionList;
                                        selectize.addOption(optionList);
                                        selectize.addOptionGroup(optionGroup[0].value, optionGroup);
                                        selectize.refreshOptions();

                                    }
                                }, 100);
                            }

                            console.log(countryList);

                        }
                    });
                </script>

            <?php } elseif ($this->settingTabs == 'apis') { ?>

                <tbody>

                    <tr>
                        <th colspan="2">
                            <h4>APIs</h4>
                            <p>Connect your shipping with third-party APIs to provide more functionalities to your customers</p>
                        </th>
                    </tr>

                    <tr>
                        <td>
                            Enable Google Places Api <br>
                            <small>Connect to your google places API to enable your customers to find and insert their address faster with Google Places API.</small> <br>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_google_places_api') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>


                    <tr>
                        <td>Google Places Api Key <br>
                            <small>Copy and paste you Google Places Api Key. <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/places-autocomplete">Learn more</a> on how to get your Google Places API key.</small>
                        </td>
                        <td>
                            <input type="text" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api_key" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api_key" value="<?php echo  esc_attr($this->get_option('shipvista_google_places_api_key') ?: '') ?>" class="sv_form-control" />
                        </td>
                    </tr>


                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_my-3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            <?php } elseif ($this->settingTabs == 'dimension') { ?>

                <!-- shipper setting -->
                <tbody>
                    <tr>
                        <th colspan="2">
                            <h4>Dimensions</h4>
                            <p>Manage fallback dimensions and weight to use for products without dimensions/weight.</p>
                        </th>
                    </tr>
                    <tr>
                        <td>Length <br>
                            <small>Default shipping length.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_length" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_length" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_length') ?: '2.5') ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>


                    <tr>
                        <td>Width <br>
                            <small>Default shipping width.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_width" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_width" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_width') ?: '2') ?>" class="sv_form-control" />
                        </td>
                    </tr>


                    <tr>
                        <td>Height <br>
                            <small>Default shipping height.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_height" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_height" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_height') ?: '1') ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>

                    <tr>
                        <td>Size unit <br>
                            <small>Default shipping size unit</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_size_unit" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_size_unit" class="custom-select">
                                <option value="cm" <?php echo  esc_attr(($this->get_option('shipvista_dimension_size_unit') == 'cm' ? 'selected' : '')); ?>>cm</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Weight <br>
                            <small>Default shipping weight.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_weight') ?: 1) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>

                    <tr>
                        <td>Weight unit <br>
                            <small>Default shipping weighing unit</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight_unit" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight_unit" class="custom-select">
                                <option value="kg" <?php echo  esc_attr(($this->get_option('shipvista_dimension_weight_unit') == 'kg' ? 'selected' : '')); ?>>kg</option>
                                <!-- <option value="lbs" <?php echo  esc_attr(($this->get_option('shipvista_dimension_weight_unit') == 'lbs' ? 'selected' : '')); ?>>lbs</option> -->
                            </select>
                        </td>
                    </tr>

                    <tr>


                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_my-3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            <?php } else { ?>
                <!-- basic settings -->
                <tbody>
                    <!-- <tr class="sv_d-none">
                        <td>Tax Status
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_tax_status" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_tax_status" class="custom-select">
                                <option value="taxable" <?php echo  esc_attr(($this->get_option('shipvista_tax_status') == 'taxable' ? 'selected' : '')); ?>>Taxable</option>
                                <option value="none">None</option>
                            </select>
                        </td>
                    </tr> -->

                    <tr class="sv_d-none">
                        <td>
                            Auto print labels <br>
                            <small>Once customers order is received print label after 15 minutes.</small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_auto_labels') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels"></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Fallback Rate <br>
                            <small>This cost will be added for every unit of products or the total order value if no shipping rates are obtained from carrier.</small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_fallback_rate" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_fallback_rate" class="sv_form-control" value="<?php echo $this->get_option('shipvista_fallback_rate') ?: 50; ?>" />
                        </td>
                    </tr>

                    <tr>
                        <td>Fallback Rate On <br>
                            <small>This cost will be added for every unit of the product if no rule is applied to it</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>fullback_rate_on" id="<?php echo  esc_attr($this->fieldPrepend) ?>fullback_rate_on" class="custom-select">
                                <option value="per_unit_quantity" <?php echo  esc_attr(($this->get_option('shipvista_fallback_rate_on') == 'per_unit_quantity' ? 'selected' : '')) ?>>Per Unit Quantity</option>
                                <option value="total_order" <?php echo  esc_attr(($this->get_option('shipvista_fallback_rate_on') == 'total_order' ? 'selected' : '')) ?>>Total Order</option>
                            </select>
                        </td>
                    </tr>



                    <tr>
                        <td>Shipping Margin (%) <br>
                            <small>Add a margin on shipping rates to be applied before displaying to customers. This is a percentage added to the total shipping cost before displaying it to the customer.<?php echo $this->get_option('shipvista_rate_margin') ?> </small>
                        </td>
                        <td>
                            <input type="number" max="100" min="0" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_rate_margin" value="<?php echo  esc_attr($this->get_option('shipvista_rate_margin')) ?>" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_rate_margin" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Handling time in days <br>
                            <small>How long does it take to get the items ready for pickup? </small>
                        </td>
                        <td>
                            <input type="number" max="100" min="0" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_handling_time" value="<?php echo  esc_attr($this->get_option('shipvista_handling_time')) ?>" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_handling_time" class="sv_form-control" />
                        </td>
                    </tr>



                    <tr class="">
                        <td>
                            Free shipping <br>
                            <small>Enable free shipping option for customers base on pre-defined calculation. </small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_free_shipping') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Free shipping Max Amount <br>
                            <small>This is the maximum amount offered for free shipping. If a shipping cost from the carrier is above this amount the balance will be displayed to customers for them to pay. Free shipping is applied to regular shipments at 100% and other shipping options at 10%.</small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_max_amount" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_max_amount" class="sv_form-control" value="<?php echo  esc_attr($this->get_option('shipvista_free_max_amount') ?: ''); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Free shipping days <br>
                            <small>Additional handling time due to free shipping. This number of days will be added to the expected number of days of delivery displayed to the customer. </small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping_days" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping_days" class="sv_form-control" value="<?php echo  esc_attr($this->get_option('shipvista_free_shipping_days') ?: ''); ?>" />
                        </td>
                    </tr>



                    <tr class="">
                        <td>
                            Enable Pickup <br>
                            <small>Enable in-store pickup.</small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_pickup') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Pickup Note<br>
                            <small>Enter pickup instructions to display to customers which they have to follow in order to come for in-store pickup.</small>
                        </td>
                        <td>
                            <textarea class="sv_form-control" class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_note" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_note"><?php echo  esc_attr($this->get_option('shipvista_pickup_note')); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>Pickup address<br>
                            <small>Enter shop locations to display to customers for pickup.</small>
                        </td>
                        <td>
                            <input type="text" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_address" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_address" value="<?php echo  esc_attr($this->get_option('shipvista_pickup_address')) ?>" class="sv_form-control" />

                        </td>
                    </tr>


                    <tr class="">
                        <td>
                            Enable API Logs <br>
                            <small>Turn this option on to log every request made by this plugin to shipvista. Logs are found in the plugin directory */assets/logs </small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo esc_attr(($this->get_option('shipvista_log_status') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status"></label>
                            </div>
                        </td>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                <!-- basic settings -->
            <?php } ?>

        </table>
    </div>
</div>