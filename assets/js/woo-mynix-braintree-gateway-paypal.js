var Mynix_PayPal = (function($, gateway, plugin_id) {
var checkout = null;
var set_fields = function(obj) {
var params = { billing_first_name : 'firstName',
billing_last_name : 'lastName',
billing_address_1 : { shippingAddress : 'streetAddress' },
billing_address_2 : { shippingAddress : 'extendedAddress' },
billing_city : { shippingAddress : 'locality' },
billing_country : { shippingAddress : 'countryCodeAlpha' },
billing_postcode : { shippingAddress : 'postalCode' },
billing_state : { shippingAddress : 'region' },
billing_phone : 'phone',
billing_email : 'email' };
for ( var key in params)
if (params.hasOwnProperty(key) && obj.hasOwnProperty(params[key])) {
if (Object.prototype.toString.call(params[key]) === '[object Object]') {
for ( var i in params[key])
if (params[key].hasOwnProperty(i) && obj[params[key]].hasOwnProperty(i))
$('#' + key).val(obj[params[key]][i]);
} else
$('#' + key).val(obj[params[key]]);
}
};
var get_fields = function() {
var params = { recipientName : [ 'billing_first_name', 'billing_last_name' ],
streetAddress : 'billing_address_1',
extendedAddress : 'billing_address_2',
locality : 'billing_city',
countryCodeAlpha2 : 'billing_country',
postalCode : 'billing_postcode',
region : 'billing_state',
phone : 'billing_phone' };
var obj = {};
for ( var key in params)
if (Object.prototype.toString.call(params[key]) === '[object Array]') {
var a = [];
for (var i = 0; i < params[key].length; i += 1)
a.push($('#' + params[key][i]).val());
obj[key] = a.join(' ');
} else
obj[key] = $('#' + params[key]).val();
return obj;
};
var init_paypal = function(token, container, onready) {
var loop = setInterval(function() {
if ($('#' + container).length) {
clearInterval(loop);
var callback = gateway.getSubmitFunction(token, false), integration_container, data, nonce_field = 'payment_method_nonce';
integration_container = 'undefined' != typeof wc_mynix_params.integration_container && wc_mynix_params.integration_container.length ? wc_mynix_params.integration_container : false;
data = { token : token,
container : container,
singleUse : 'undefined' != typeof wc_mynix_params.paypal_flow ? wc_mynix_params.paypal_flow : false,
amount : $('#' + plugin_id + '-order-amount').val(),
currency : wc_mynix_params.currency,
locale : wc_mynix_params.paypal_locale,
onPaymentMethodReceived : function(obj) {
if (integration_container) {
$('#' + integration_container).hide('slow');
if ('undefined' == typeof wc_mynix_params.paypal_opacity || wc_mynix_params.paypal_opacity)
$('#' + container).parent().toggleClass('has-paypal');
}
if ('custom' == gateway.get_integration_type() && 'undefined' != typeof Mynix_Custom_UI) {
Mynix_Custom_UI.toggle_card('paypal');
}
if (!$('input[name="' + nonce_field + '"]').length)
$('<input>').attr({ type : 'hidden',
id : nonce_field,
name : nonce_field,
value : obj.nonce }).appendTo('form.checkout,form#order_review');
if ('undefined' != typeof obj.details)
set_fields(obj.details);
gateway.paypal_button = true;
},
onUnsupported : function() {
gateway.mynixShowError('Your browser does not support this payment method.');
},
onCancelled : function() {
gateway.paypal_button = false;
$('input[name="' + nonce_field + '"]').val('');
if (integration_container) {
if ('undefined' == typeof wc_mynix_params.paypal_opacity || wc_mynix_params.paypal_opacity)
$('#' + container).parent().toggleClass('has-paypal');
$('#' + integration_container).show('slow', function() {
gateway.mynixShowError('The choosen payment method was canceled by user intervention.');
});
}
if ('custom' == gateway.get_integration_type() && 'undefined' != typeof Mynix_Custom_UI) {
Mynix_Custom_UI.toggle_card();
}
},
onReady : function(integration) {
checkout = integration;
$('#' + container).parent().find('label').css('display', 'inherit');
('function' == typeof onready) && onready();
} };
if ('undefined' != typeof wc_mynix_params.paypal_displayName && wc_mynix_params.paypal_displayName.length)
data.displayName = wc_mynix_params.paypal_displayName;
if ('undefined' == typeof wc_mynix_params.disable_shippingAddressOverride || !wc_mynix_params.disable_shippingAddressOverride) {
data.shippingAddressOverride = get_fields();
data.shippingAddressOverride.editable = true;
data.shippingAddressOverride.type = $('#billing_company').val().length ? 'business' : 'residential';
}
gateway.init(data, 'paypal');
}
}, 1000);
};
var teardown_paypal = function() {
checkout && checkout.teardown(function() {
checkout = null;
});
};
return { init : init_paypal,
set_fields : set_fields,
get_fields : get_fields,
teardown : teardown_paypal };
})(jQuery, Mynix_Gateway, 'woo-mynix-braintree-gateway');