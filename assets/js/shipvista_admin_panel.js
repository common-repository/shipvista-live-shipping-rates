/**
 * Declare global variables
 */
var idAppend = 'woocommerce_shipvista_';
var sv_apiEndPoint = '';
var sv_apiToken = '';
var sv_alertBar = `<div id="shipvista_alertBar" class="shipvista_alertBar"><div id="shipvista_alertBar_content"></div></div>`;
var sv_action_link = '';
var sv_carrierSettings = {};
var sv_apiUrl = 'https://api.shipvista.com/';
/**
 * Return element content based on id
 * @param {*} id 
 */
function _id(id) {
    return document.getElementById(id);
}


function removeShippingMethod(id) {
    if (id >= 0) {
        var con = confirm("Are you sure you wish to delete this shipping method?");
        if (con) {
            var form = getInput('shipvista_custom_shipping_method');
            if (form) {
                try {
                    jQuery('#address_' + id).fadeToggle();
                    form = JSON.parse(form);
                    form.splice(id, 1);
                    _id(idAppend + 'shipvista_custom_shipping_method').value = JSON.stringify(form);
                    setTimeout(() => {
                        sv_WooSave();
                    }, 500);
                } catch (e) {
                    alertBar('Could not load shipping method');
                    console.log(e);
                }
            } else {
                alertBar('Could not find shipping method');
            }
        }
    } else { 
        alertBar("Could not find shipping method to delete.");
    }
}

function addShippingMethod() {
    var method = _id('shipping_method').value;
    var shippingObject = {};
    var displayName = _id('_display_name').value;
    var deliveryDays = _id('_shipping_method_days').value;
    shippingObject.displayName = displayName;
    shippingObject.deliveryDays = deliveryDays;

    if (method == '_flat') {
        var cost = _id('_shipping_method_cost').value;
        var fallback = _id('_only_fallback').checked ? 1 : 0;
        shippingObject.type = 'flat'
        shippingObject.fallback = fallback;
    } else { // is free shipping
        shippingObject.type = 'free';
        var requires = _id('free_shipping_require').value;
        var cost = _id('_free_shipping_method_cost').value;
        shippingObject.requires = requires;
    }

    if (displayName.length > 3) {
        if (cost > 0) {
            shippingObject.cost = cost;

            var shippingRates = getInput('shipvista_custom_shipping_method');
            try {
                shippingRates = JSON.parse(shippingRates);
            } catch (e) {
                shippingRates = [];
            }

            shippingRates.push(shippingObject);
            // stringyfy object
            _id(idAppend + 'shipvista_custom_shipping_method').value = JSON.stringify(shippingRates);
            sv_WooSave();

        } else {
            alertBar('Enter a valid cost bar.');
        }
    } else {
        alertBar('Enter a valid display name.')
    }
}

window.addEventListener('load', evt => {



    // handle custom shipping methods
    if (_id('shipping_method')) {
        _id('shipping_method').addEventListener('change', (e) => {
            jQuery('._shippingMethods').addClass('sv_d-none');
            _id(e.target.value).classList.remove('sv_d-none');
        });
    }

    // handle custom shipping methods


















    if (_id('to_country')) {
        _id('from_country').addEventListener('change', e => {
            var country = e.target.value;
            // reset states
            _id('from_state').disabled = true;
            _id('from_state').innerHTML = `<option value="">Select State</option>`;
            if (country.length == 2) {
                jQuery.post(
                    window.location,
                    {
                        'shipvista_post_request': 'getStates',
                        country,
                    },
                    (response) => {
                        if (response.status) {
                            _id('from_state').disabled = false;
                            // set states
                            var stateCodes = Object.keys(response.states);
                            for (let index = 0; index < stateCodes.length; index++) {
                                const state = response.states[stateCodes[index]];
                                let option = `<option value="${stateCodes[index]}">${state}</option>`;
                                jQuery('#from_state').append(option);
                            }
                        }
                    }
                ).fail(e => {
                    alertBar('There was an error selecting country');
                    console.log(e);
                });
            } else {
                alertBar('Select a valid country');
            }
        });
    }

});

function toggleAddressForm(reset = 1) {
    if (reset) {
        _id('mainform').reset();
        document.querySelector('.addressButton button').innerText = 'Save Address';
        if (reset === true) {
            var $select = jQuery('#to_state').selectize();
            selectize = $select[0].selectize;
            selectize.setValue([]);
            var $select = jQuery('#to_country').selectize();
            selectize = $select[0].selectize;
            selectize.setValue([]);

        }

    }
    if (isAddrebookUpdate === false) {
        isAddrebookUpdate = false;
        jQuery('._form_element').toggleClass('d-none');
    }
}

function setOptionSelected(id, value, multi = 0) {
    var option = _id(id).querySelectorAll('option');
    var selectize = '';
    if (id == 'to_country' || id == 'to_state') {
        var $select = jQuery('#' + id).selectize();
        selectize = $select[0].selectize;
        selectize.setValue(value);

    } else {
        for (let index = 0; index < option.length; index++) {
            const element = option[index];
            if (element.value == value) {
                element.selected = true;
            } else {
                if (!multi) {
                    element.selected = false;
                }
            }

        }
    }
}

function cloneLocation(id) {
    editLocation(id);
    isAddrebookUpdate = false;
    document.querySelector('.addressButton button').innerText = 'Save Address';

}

function editLocation(id) {
    if (id >= 0) {
        // fill in the form
        var form = _id('_address_' + id).getAttribute('data-content');
        if (form) {
            try {
                form = JSON.parse(form);
                setOptionSelected('from_country', form.from_address.country);
                _id('_nickname').value = form.nickname;

                // Create a new 'change' event
                var event = new Event('change');
                // Dispatch it.
                _id('from_country').dispatchEvent(event);
                setTimeout(() => {
                    setOptionSelected('from_state', form.from_address.state);
                }, 1000);
                _id('from_city').value = form.from_address.city;
                _id('from_zip_code').value = form.from_address.zip_code;

                var countriesTo = form.to_address.country || [];

                setTimeout(() => {
                    setOptionSelected('to_country', countriesTo, 1);
                }, 1000);

                setTimeout(() => {
                    var stateList = [];
                    for (let country in form.to_address.state) {
                        const states = form.to_address.state[country];
                        for (let i = 0; i < states.length; i++) {
                            const el = states[i];
                            stateList.push(country + '.' + el);
                        }
                    }
                    setOptionSelected('to_state', stateList, 1);
                }, 2000);

                _id('to_state').value = form.to_address.state || '';
                _id('to_zip_codes').value = form.to_address.zip_code || '';
                document.querySelector('.addressButton button').innerText = 'Update Address';
                toggleAddressForm(0);
                isAddrebookUpdate = id - 1;

            } catch (e) {
                alertBar('Could not load location data');
            }
        } else {
            alertBar('Could not find location data');
        }
    } else {
        alertBar("Could not find location edit");
    }
}

function deleteLocation(id) {
    if (id >= 0) {
        var con = confirm("Are you sure you wish to delete this location?");
        if (con) {
            var form = getInput('shipvista_address_book');
            if (form) {
                try {
                    jQuery('#address_' + id).fadeToggle();
                    form = JSON.parse(form);
                    form.splice((id - 1), 1); // account for default location
                    _id(idAppend + 'shipvista_address_book').value = JSON.stringify(form);
                    setTimeout(() => {
                        sv_WooSave();
                    }, 1000);
                } catch (e) {
                    alertBar('Could not load location data');
                    console.log(e);
                }
            } else {
                alertBar('Could not find location data');
            }
        }
    } else {
        alertBar("Could not find location to delete");
    }
}

var isAddrebookUpdate = false;
function sv_saveAddressBook() {
    var f_country = (_id('from_country').value).replace(/[^a-zA-Z]/gi, '');
    var f_state = (_id('from_state').value).replace(/[^a-zA-Z]/gi, '');
    var f_zip = (_id('from_zip_code').value).replace(/[^a-zA-Z0-9]/gi, '');
    var f_city = (_id('from_city').value).replace(/[^a-zA-Z0-9 ]/gi, '');
    var nickName = (_id('_nickname').value).replace(/[^a-zA-Z0-9\_\.\- ]/gi, '');
    if (f_country.length == 2) {
        if (f_state.length >= 2) {
            if (f_city.length >= 3) {
                if (f_zip.length > 4) {
                    var address = getInput('shipvista_address_book');
                    try {
                        address = JSON.parse(address);
                    } catch (e) {
                        address = [];
                    }
                    var to_country = jQuery('#to_country').val();
                    // if (to_country.length > 0) {
                    var stateList = {};
                    var states = jQuery('#to_state').val().map(x => {
                        var state = x.replace(/[^a-zA-Z\.]/gi, '');
                        var split = state.split('.');
                        if (split.length == 2) {
                            if (Object.keys(stateList).indexOf(split[0]) >= 0) {
                                stateList[split[0]].push(split[1]);
                            } else {
                                stateList[split[0]] = [split[1]];
                            }
                        }

                    });
                    const object = {
                        nickname: nickName,
                        from_address: {
                            country: f_country,
                            state: f_state,
                            zip_code: f_zip,
                            city: f_city
                        },
                        to_address: {
                            country: to_country,
                            state: stateList,
                            zip_code: (_id('to_zip_codes').value).replace(/[^a-zA-Z0-9,]/gi, '')
                        }
                    };

                    if (isAddrebookUpdate !== false) {
                        address[isAddrebookUpdate] = object;
                    } else {
                        address.push(object);
                    }

                    var addressString = JSON.stringify(address);
                    // insert address to form
                    _id(idAppend + 'shipvista_address_book').value = addressString;
                    // save form
                    sv_WooSave();
                    // } else {
                    //     alertBar('Select at least delivery country');
                    // }
                } else {
                    alertBar('Enter a valid zip code');
                }
            } else {
                alertBar('Enter a valid city. At least 3 characters.');
            }
        } else {
            alertBar('Enter valid state code. Only two characters.')
        }
    } else {
        alertBar('Enter a valid country. Only two characters');
    }
}



/**
 * Get form parts from wordpress input fields
 */
function getInput(id) {
    return _id(idAppend + id).value;
}

/**
 * Manage form error
 * @param id
 * @param msg
 */
function inputError(id, msg) {
    try {
        if (id) {
            var input = idAppend + id;
            scrollToInput(input);
            jQuery('#' + input).addClass('is-invalid');
            jQuery('#' + input).removeClass('mb-3');

            // check for error container
            var parent = _id(input).parentElement;
            var errorId = input + '_msg';
            var span = parent.querySelector('#' + errorId) != undefined;
            if (span != undefined) {
                span = document.createElement('div');
                span.setAttribute('id', errorId);
                span.setAttribute('class', 'text-danger mb-3');
                parent.appendChild(span);
            }
            jQuery('#' + errorId).html(msg)
            // remove error from page after view
            setTimeout(() => {
                jQuery('#' + input).removeClass('is-invalid');
                jQuery('#' + input).addClass('mb-3');
                jQuery('#' + errorId).html('')
            }, 5000);
        } else {
            alertBar('alert');
        }
    } catch (e) {
        console.log(e);
    }
}


function alertBar(msg, cls) {
    if (!msg) return false;

    if (!cls) {
        cls = 'bg-danger';
    }

    var errorBar = _id('shipvista_alertBar');
    if (errorBar == undefined) {
        jQuery('body').append(sv_alertBar);
    }
    // add snackbar class
    jQuery('#shipvista_alertBar').addClass(cls);
    // dispaly alert bar

    jQuery('#shipvista_alertBar').addClass('show');
    jQuery('#shipvista_alertBar_content').html(msg);
    setTimeout(() => {
        jQuery('#shipvista_alertBar').attr('class', " ");
    }, 5000);

}

/**
 * Scroll to error form
 */
function scrollToInput(id) {
    _id(id).parentNode.scrollIntoView();
    _id(id).scrollIntoView(true);

    // add space to inview
    var scrolledY = window.scrollY;
    if (scrolledY) {
        window.scroll(0, scrolledY - 100);
    }

}

/**
 * Call shipvista api
 */
function svApiCall(content, endPoint, meth, callback) {
    if (!meth) meth = 'post';
    endPoint = sv_apiUrl + endPoint;
    var result = {
        status: 0,
        message: ''
    };
    // if (Object.keys(content).length > 0) {
    jQuery.ajax({
        url: endPoint,
        type: meth,
        data: JSON.stringify(content),
        contentType: 'application/json',
        dataType: 'json',
        headers: {
            Authorization: 'Bearer ' + sv_apiToken,
        },
        success: function (data, textStatus, xhr) {
            if (xhr.status == 200 || xhr.status == 'success') {
                if (Object.keys(data).indexOf('status') >= 0) {
                    result = data;
                } else if (Object.keys(data).indexOf('response') >= 0) {
                    result = data.response;

                } else {
                    data.status = true;
                    result = data;
                }
            } else {
                result.xhr = xhr.status;
                result.message = data.responseText;
            }
            if (callback.length > 0) {
                // find object
                var fn = window[callback];
                // is object a function?
                if (typeof fn === "function") fn(result);
            } else {
                return result;
            }
        },
        error: function (data) {
            result.message = data.responseText;
            if (callback.length > 0) {
                // find object
                var fn = window[callback];
                // is object a function?
                if (typeof fn === "function") fn(result);
            } else {
                return result;
            }
            return false;
        }
    });
    // } else {
    //     result.message = 'Invaild content';
    //     return result;
    // }
}

/**
 * Connect shop to shipvista
 * 
 */
function shipvista_ConnectStore(callback = false) {
    if (callback != false) {
        result = callback;
        if (result.status == true || result?.access_token?.tokenString) {
            alertBar('Login successfull', 'bg-success');
            // set form data
            jQuery('#' + idAppend + 'shipvista_api_token').val(result.access_token.tokenString);
            jQuery('#' + idAppend + 'shipvista_refresh_token').val(result.refresh_token.tokenString);
            jQuery('#' + idAppend + 'shipvista_token_expires').val(result.access_token.expireAt);
            jQuery('#' + idAppend + 'shipvista_user_avatar').val(result.avatar);
            jQuery('#' + idAppend + 'shipvista_user_balance').val(0);
            jQuery('#' + idAppend + 'shipvista_user_name').val(result.refresh_token.username);
            jQuery('#' + idAppend + 'shipvista_user_currency').val('USD');
            // set form data
            sv_WooSave();
        } else {
            alertBar(result.message, 'bg-danger');
        }
    } else {
        var email = getInput('shipvista_user_name');
        var password = getInput('shipvista_user_pass');
        if (email.length > 1 && email.length > 3) {
            if (password.length > 4) {
                var content = {
                    user_id: email,
                    password: password
                };

                svApiCall(content, '/api/Login', 'POST', 'shipvista_ConnectStore');

            } else {
                inputError('shipvista_user_pass', 'Enter a valid shipvista account password');
            }
        } else {
            inputError('shipvista_user_email', 'Enter a valid email.');
        }
    }
}

function sv_WooSave() {
    document.getElementsByClassName('woocommerce-save-button')[0].click();
}



// Unlink user account
function shipvista_unlinkAccount() {
    var status = confirm("Are you sure you want to unlink shipvista?");
    if (status == false) {
        _id('woocommerce_shipvista_enabled').checked = true;
        return false;
    }
    _id('woocommerce_shipvista_enabled').checked = false;
    alertBar('Thank you for using shivista.', 'bg-info');
    setTimeout(() => {
        // save 
        sv_WooSave();
    }, 3000);

}

function shipvista_carrierSelectOption(carrier, key) {
    if (!carrier || !key) return false;
    var id = carrier + '_' + key;
    var input = _id(id);
    var carrierStatus = _id(carrier).checked;
    if (carrierStatus != true) {
        _id(carrier).checked = true;
    }
    // var status = _id(key).checked ? 1 : 0;

    input.disabled = false;
    var name = input.getAttribute('data-shipvista-name');
    if (input.checked == true) {
        // get name
        // set values
        sv_carrierSettings[carrier].push(name);
        // sv_carrierSettings[carrier][key]['checked'] = status;

    } else {
        // remove it from the table
        if (sv_carrierSettings[carrier].indexOf(name) !== false) {
            sv_carrierSettings[carrier].splice(sv_carrierSettings[carrier].indexOf(name), 1);
        }
    }


    jsonText = JSON.stringify(sv_carrierSettings[carrier]);
    _id(idAppend + carrier).value = jsonText;
    // } else {
    //     _id(key).checked = false;
    //     alertBar('Please enable carrier to select this method', 'bg-warning');
    // }

}

function shipvista_toggleCarrieSubs(carrier, act) {
    if (!carrier) return false;

    var options = document.getElementsByClassName(carrier + '_options');
    if (options.length > 0) {
        for (let index = 0; index < options.length; index++) {
            const parent = options[index];
            var input = parent.getElementsByTagName('input')[0];
            input.checked = act;
            if (act == true) {
                input.disabled = false;
                // get name
                var name = input.getAttribute('data-shipvista-name');
                // var key = input.getAttribute('name');
                // set values
                sv_carrierSettings[carrier].push(name);
                // sv_carrierSettings[carrier][key]['checked'] = 1;
            } else {
                input.disabled = true;
            }
        }
    }

    var jsonText = '';
    if (act == true) {
        jsonText = JSON.stringify(sv_carrierSettings[carrier]);
    }
    _id(idAppend + carrier).value = jsonText;

}

function shipvista_toggleCarrierOption(carrier) {
    if (!carrier) return false;

    var status = _id(carrier).checked;
    if (status == false) {
        shipvista_toggleCarrieSubs(carrier, false);
        _id(idAppend + carrier + '_enabled').value = 'no';
    } else {
        // check if the item exist
        if (Object.keys(sv_carrierSettings).indexOf(carrier) == undefined) {
            sv_carrierSettings[carrier] = {};
        }
        _id(idAppend + carrier + '_enabled').value = 'yes';

        // check if the item exist
        shipvista_toggleCarrieSubs(carrier, true);
    }
}


function shipvista_saveSettings(ell) {

    if (ell == 'shipper') {
        _id(idAppend + 'shipvista_origin_country').value = _id('shipvista_origin_country').value;
        _id(idAppend + 'shipvista_origin_address').value = _id('shipvista_origin_address').value;
        _id(idAppend + 'shipvista_origin_city').value = _id('shipvista_origin_city').value;
        _id(idAppend + 'shipvista_origin_postcode').value = _id('shipvista_origin_postcode').value;
        _id(idAppend + 'shipvista_origin_phone_number').value = _id('shipvista_origin_phone_number').value;
        sv_WooSave();
    }

}


function svToggleClass(id, toggle) {
    if (id) {
        if (!toggle) {
            toggle = 'd-none'
        }
        jQuery('#' + id).toggleClass(toggle);
    }
}



var isMore = false;

function shipvistaToggleViewMoreList() {
    // jQuery('.shipvista_list_hide').toggleClass('sv_d-none');
    var ell = document.getElementsByClassName('shipvista_list_hide');
    for (let index = 0; index < ell.length; index++) {
        const element = ell[index];
        element.classList.toggle("sv_d-none");

    }
    if (isMore == false) {
        document.getElementById('_shipvistaMoreList').innerHTML = 'LESS <i class="fa fa-chevron-up"></i>';
        isMore = true
    } else {
        document.getElementById('_shipvistaMoreList').innerHTML = 'MORE <i class="fa fa-chevron-down"></i>';
        isMore = false;
    }
}

function shipvistaSubmitlabelCreate() {
    document.getElementById('shipvistaLabel_get_label').value = 1;
    document.getElementById('shipvista_shipping_carrier').value = document.querySelector('input[name=shipvista_shipping_method]:checked').getAttribute('data-carrier');
    document.getElementById('shipvista_shipping_options').value = document.querySelector('input[name=shipvista_shipping_method]:checked').getAttribute('data-carrier-option');
    document.getElementById('post').submit();
}


function toggleAccountCreate() {
    jQuery('#_setupAccount').toggleClass('d-none');
    jQuery('#_createAccount').toggleClass('d-none');
}

function createShipvistaUserAccount(callback = false) {
    if (callback != false) {

        if (callback.status == true) {
            alertBar(callback.message, 'bg-success');
            setCookie('shipvista_user_pending_auth', '', 1);
            jQuery('#_signupCont').toggleClass('d-none');
            jQuery('#_verifyCont').toggleClass('d-none');
            jQuery('#_verifyEmail').html(getCookie('shipvista_user_email'));
            jQuery('#_verifyName').html(getCookie('shipvista_user_names'));
        } else {
            alertBar(callback.message, 'bg-danger');
            setCookie('shipvista_user_email', '', -1);
            setCookie('shipvista_user_names', '', -1);
        }
    } else {
        var name = getInput('create_user_names');
        var email = getInput('create_user_email');
        var phone = getInput('create_user_phone');
        var pass = getInput('create_user_password');
        // run creation of details
        if (name.length > 4) {
            if (email.length > 3 && email.split('@').length == 2) {
                if (phone.length > 7) {
                    if (phone.length > 7) {
                        // store cookie
                        setCookie('shipvista_user_email', email, 1);
                        setCookie('shipvista_user_names', name, 1);
                        svApiCall({
                            'contact_name': name,
                            'email': email,
                            'phone': phone,
                            'password': pass,
                            'verficationEmail': getInput('create_user_link')
                        }, '/api/register', 'POST', 'createShipvistaUserAccount');

                    } else {
                        inputError('create_user_password', 'Your password must be atleast 8 characters.');
                    }
                } else {
                    inputError('create_user_phone', 'Please enter a valid phone number.')
                }
            } else {
                inputError('create_user_email', 'Please enter a valid email address');
            }
        } else {
            inputError('create_user_names', 'Please enter a valid name.');
        }
    }
}




function changeSignupDetails() {
    setCookie('shipvista_user_email', '', -1);
    setCookie('shipvista_user_names', '', -1);
    setCookie('shipvista_user_pending_auth', '', -1);
    alertBar('Authentication reseted.', 'bg-info');
    window.location = '';
}
































// system functions
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function checkCookie() {
    var user = getCookie("username");
    if (user != "") {
        alert("Welcome again " + user);
    } else {
        user = prompt("Please enter your name:", "");
        if (user != "" && user != null) {
            setCookie("username", user, 365);
        }
    }
}