/**
 * ################################################################################
 * Woo Braintree Payment
 * 
 * Copyright 2016 Eugen Mihailescu <eugenmihailescux@gmail.com>
 * 
 * This program is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 * ################################################################################
 * 
 * Short description:
 * URL: http://woo-braintree.mynixworld.info
 * 
 * Git revision information:
 * 
 * @version : 0.2-1 $
 * @commit  : d1f5967ad241079d6f213ec43cb555f2430423c8 $
 * @author  : eugenmihailescu <eugenmihailescux@gmail.com> $
 * @date    : Thu Dec 8 12:16:22 2016 +0100 $
 * @file    : woo-mynix-braintree-gateway.js $
 * 
 * @id      : woo-mynix-braintree-gateway.js | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

"use strict";
var Mynix_Gateway = (function($, plugin_id, dbg) {
var threads = 0;
var client = null;
var paypal_button = false;
var unblock_modal = function() {
('undefined' != typeof Mynix_3DS) && Mynix_3DS.unblock_modal();
}
var get_integration_type = function() {
return 'undefined' != typeof wc_mynix_params.integration_type ? wc_mynix_params.integration_type : 'custom';
};
var mynixShowError = function(error) {
if (Object.prototype.toString.call(error) === '[object Array]')
error = error.join("<br>");
else if (Object.prototype.toString.call(error) === '[object Object]')
error = error.message;
else if (!error.toString().length) {
error = "Unknown error.";
}
error = '<div class="woocommerce-error woocommerce_error">' + error.toString() + '</div>';
$('.woocommerce-error, .woocommerce-message, .woocommerce_error').remove();
$('form.checkout, form#order_review').prepend(error);
$('form.checkout, form#order_review').removeClass('processing').unblock();
$('form.checkout, form#order_review').find('.input-text, select').blur();
$('html, body').animate({ scrollTop : ($('form.checkout').offset().top - 100) }, 1000);
$(document.body).trigger('checkout_error');
$('form.checkout, form#order_review').unblock();
unblock_modal();
return false;
}
var mynixClientGetToken = function(callback) {
$.ajax({ url : wc_mynix_params.ajaxurl,
type : 'POST',
dataType : 'json',
data : { action : 'send_client_token',
mynix_nonce : wc_mynix_params.mynix_nonce },
error : function(jqXHR, textStatus, errorThrown) {
mynixShowError([ textStatus, errorThrown ]);
},
success : callback });
};
function mynixSetup(data, integration) {
integration = 'undefined' == typeof integration ? get_integration_type() : integration;
var form = $('form.checkout, form#order_review');
if (data.hasOwnProperty('error')) {
return mynixShowError(data.error);
}
if (form.length && !form[0].id.length) {
form[0].id = plugin_id + '-checkout';
data.id = form[0].id;
}
$('#payment').block({ message : null,
overlayCSS : { background : '#fff',
opacity : 0.6 } });
var default_onready = 'undefined' != typeof data.onReady ? data.onReady : false;
data.onReady = function(integration) {
default_onready && default_onready(integration);
$('#payment').unblock();
};
data.onError = function(error) {
mynixShowError(error);
};
braintree.setup(data.token, 'hosted' == integration ? 'custom' : integration, data);
Mynix_Gateway.client = new braintree.api.Client({ clientToken : data.token });
}
var mynixSubmitOrder = function(err, nonce) {
if (Object.prototype.toString.call(nonce) === '[object Object]')
nonce = nonce.nonce;
if ($("input[name=payment_method_nonce]").length > 1)
$('input[name=payment_method_nonce]').val(nonce);
var form = $('form.checkout, form#order_review');
if (null !== err) {
mynixShowError(err);
} else {
$('<input>').attr({ type : 'hidden',
id : 'woocommerce_checkout_place_order',
name : 'woocommerce_checkout_place_order',
value : true }).appendTo(form);
$('<input>').attr({ type : 'hidden',
id : 'payment_method_nonce',
name : 'payment_method_nonce',
value : nonce }).appendTo(form);
if('undefined'!==typeof wc_mynix_params.submit_order && wc_mynix_params.submit_order.trim().length){
$('form.checkout, form#order_review').block({ message : wc_mynix_params.submit_order, blockMsgClass:'mynix-blockUI',
overlayCSS : { background : '#fff',
opacity : 0.6 } });
}
form.submit();
}
}
var getSubmitFunction = function(token, supports_3ds) {
supports_3ds = 'undefined' == typeof supports_3ds ? true : supports_3ds;
var is3DS = 'undefined' != typeof wc_mynix_params.braintree_3ds && 'true' == wc_mynix_params.braintree_3ds;
if (supports_3ds && is3DS) {
try {
var obj = JSON.parse(atob(token));
wc_mynix_params.avs = 'undefined' != typeof obj.challenges.postal_code || 'undefined' != typeof obj.challenges.street_address;
} catch (e) {
}
}
if (supports_3ds && is3DS && ('undefined' != typeof Mynix_3DS)) {
return Mynix_3DS.mynix3DSFilter;
}
return mynixSubmitOrder;
};
$(function() {
$('body').on('checkout_error', function() {
unblock_modal();
});
$(window).load(function() {
'undefined' != typeof $('.help_tip').data('events') || setTimeout(function() {
$('.help_tip').tipTip();
}, 2000);
});
});
return { client : client,
mynixClientGetToken : mynixClientGetToken,
mynixShowError : mynixShowError,
mynixSubmitOrder : mynixSubmitOrder,
init : mynixSetup,
getSubmitFunction : getSubmitFunction,
unblock_modal : unblock_modal,
paypal_button : paypal_button,
threads : threads,
get_integration_type : get_integration_type };
}(jQuery, 'woo-mynix-braintree-gateway', 'undefined' != typeof Mynix_Console_Debug ? Mynix_Console_Debug : false));