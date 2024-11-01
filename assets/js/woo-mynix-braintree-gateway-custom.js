var Mynix_Custom_UI = (function($, plugin_id, gateway, dbg) {
var toggle_active_card_badge = function(card_type) {
card_type = 'undefined' == typeof card_type ? '' : card_type;
$('.card-badge').each(function() {
var badge = $(this);
if (card_type.length) {
$.each(badge.attr('class').split(/\s+/), function(index, item) {
item = item.replace('-badge', '');
switch (item) {
case 'amex':
case 'visa':
case 'mastercard':
case 'discover':
case 'dinersclub':
case 'jcb':
case 'maestro':
case 'paypal':
return card_type != item && badge.addClass('disabled') || badge.removeClass('disabled');
}
});
} else
badge.removeClass('disabled');
});
};
var mynixCheckCard = function() {
var card = $('#' + plugin_id + '-card-number').val(), expiry = $('#' + plugin_id + '-card-expiry').val(), cvv = $('#' + plugin_id + '-card-cvc');
card = card.replace(/\s/g, '');
expiry = expiry.replace(/\s/g, '');
cvv = cvv.length ? cvv.val() : false;
var card_e = card.length < 12 || card.length > 19;
var expiry_e = expiry.length !== 5 && expiry.length !== 7;
var cvv_e = false !== cvv && (cvv.length < 3 || cvv.length > 4);
if (card_e) {
$('#' + plugin_id + '-card-number').effect("shake", { direction : 'right',
distance : 10 });
}
if (expiry_e) {
$('#' + plugin_id + '-card-expiry').effect("shake", { distance : 10 });
}
if (cvv_e) {
$('#' + plugin_id + '-card-cvc').effect("shake", { direction : 'right',
distance : 10 });
}
return !(card_e || expiry_e || cvv_e);
}
var tokenize_card = function(callback) {
var card = $('#' + plugin_id + '-card-number').val(), cvv = $('#' + plugin_id + '-card-cvc'), expiry = $('#' + plugin_id + '-card-expiry').val();
card = card.replace(/\s/g, '');
expiry = expiry.replace(/\s/g, '');
cvv = cvv.length ? cvv.val() : false;
var card_obj = { number : card,
expirationDate : expiry };
if (false !== cvv) {
card_obj.cvv = cvv;
}
gateway.client.tokenizeCard(card_obj, callback);
}
var mynixServerTokenReceived = function(data) {
gateway.init(data);
tokenize_card(gateway.getSubmitFunction(data.token));
};
function mynixFormHandler() {
if ($('#payment_method_' + plugin_id).is(':checked')) {
var form = $('form.checkout, form#order_review');
if (gateway.paypal_button || mynixCheckCard()) {
if (!$('input[name=payment_method_nonce]').val()) {
gateway.mynixClientGetToken(mynixServerTokenReceived);
return false;
} else {
$('.' + plugin_id + '-card-field').val('');
gateway.unblock_modal();
return true;
}
} else {
return false;
}
}
return true;
}
$(function() {
var submit_callback = function() {
return mynixFormHandler();
};
$('form.checkout').on('checkout_place_order_' + plugin_id, submit_callback);
$('form#order_review').on('submit', submit_callback);
$('form.checkout, form#order_review').on('change', '#' + plugin_id + '-cc-form :input', function() {
$("input[name=payment_method_nonce]").val('');
});
$('form.checkout, form#order_review').on('keyup', '#' + plugin_id + '-card-number', function() {
var card_type = '';
if ($(this).hasClass('identified')) {
$.each($(this).attr('class').split(/\s+/), function(index, item) {
switch (item) {
case 'amex':
case 'visa':
case 'mastercard':
case 'discover':
case 'dinersclub':
case 'jcb':
case 'maestro':
case 'paypal':
return card_type = item;
}
});
} else if ('undefined' != typeof Mynix_CC_Validator) {
var obj = Mynix_CC_Validator;
obj.init($('#' + plugin_id + '-card-number').val().replace(' ', ''));
switch (obj.is_valid_issuer(true)) {
case obj.AMEX:
card_type = 'amex';
break;
case obj.VISA:
card_type = 'visa';
break;
case obj.MASTER:
card_type = 'mastercard';
break;
case obj.DISCOVER:
card_type = 'discover';
break;
case obj.DINERS:
card_type = 'dinersclub';
break;
case obj.JCB:
card_type = 'jcb';
break;
case obj.MAESTRO:
card_type = 'maestro';
break;
case obj.PAYPAL:
card_type = 'paypal';
break;
}
}
toggle_active_card_badge(card_type);
});
$('form.checkout, form#order_review').on('input propertychange', '#' + plugin_id + '-card-expiry', function() {
if (2 == $(this).val().length)
$(this).val($(this).val() + '/');
});
});
return { mynixCheckCard : mynixCheckCard,
tokenize_card : tokenize_card,
mynixServerTokenReceived : mynixServerTokenReceived,
toggle_card : toggle_active_card_badge };
})(jQuery, 'woo-mynix-braintree-gateway', Mynix_Gateway, 'undefined' != typeof Mynix_Console_Debug ? Mynix_Console_Debug : false);