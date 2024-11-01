<?php
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
 * @file    : woo-mynix-braintree.php $
 * 
 * @id      : woo-mynix-braintree.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

! @constant('ABSPATH') && exit();
! @constant('WP_DEBUG') && define('\WP_DEBUG', true);
! @constant('WP_DEBUG_LOG') && define('\WP_DEBUG_LOG', true);
! @constant('WP_DEBUG_DISPLAY') && define('\WP_DEBUG_DISPLAY', true);
! @constant('SCRIPT_DEBUG') && define('\SCRIPT_DEBUG', false);
global $woocommerce;
if (isset($woocommerce) && ! class_exists('Woo_Mynix_Braintree_Gateway')) {
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config.php';
require_once 'woo-mynix-payment-gateway.php';
require_once 'woo-mynix-utils.php';
include_once \ABSPATH . '/wp-admin/includes/plugin.php';
class Woo_Mynix_Braintree_Gateway extends Woo_Mynix_Payment_Gateway
{
const GROUP_OPTION_CUSTOM = 1;
const GROUP_OPTION_COMMON = 2;
const GROUP_OPTION_PAYPAL = 3;
const GROUP_OPTION_DROPIN = 4;
const VENDOR = 'Braintree';
const VENDOR_GATEWAY = 'braintreegateway.com';
const VENDOR_URL = 'braintreepayments.com';
const VENDOR_BADGE_URI = 'https://s3.amazonaws.com/braintree-badges/';
const VENDOR_BADGE_ICON = 'braintree-badge-dark.png';
private $_public_key = '';
private $_private_key = '';
private $_merchant_id = '';
private $_sandbox = false;
private $_has_jquery_payment = false;
private $_paypal_container = 'paypal-container';
protected $_braintree_container = 'braintree-container';
protected $VENDOR_DOCS_URL;
protected $VENDOR_CPANEL_URL;
protected $VENDOR_FAQ_URL;
public $VENDOR_DEVELOPER_URL;
protected function _fallback_credit_card_form($args = array(), $fields = array())
{
$input_type = $this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM) ? 'input' : 'div';
$default_args = array(
'fields_have_names' => true
);
$args = wp_parse_args($args, apply_filters('woocommerce_credit_card_form_args', $default_args, $this->id));
$default_fields = array(
'card-number-field' => '<p class="form-row form-row-wide">
<label for="' . esc_attr($this->id) . '-card-number">' . __('Card Number', 'woocommerce') . ' <span class="required">*</span></label>
<' . $input_type . ' id="' . esc_attr($this->id) . '-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="' . ($args['fields_have_names'] ? $this->id . '-card-number' : '') . '"></' . $input_type . '>
</p>',
'card-expiry-field' => '<p class="form-row form-row-first">
<label for="' . esc_attr($this->id) . '-card-expiry">' . __('Expiry (MM/YYYY)', 'woocommerce') . ' <span class="required">*</span></label>
<' . $input_type . ' id="' . esc_attr($this->id) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__('MM / YYYY', 'woocommerce') . '" name="' . ($args['fields_have_names'] ? $this->id . '-card-expiry' : '') . '"></' . $input_type . '>
</p>',
'card-cvc-field' => '<p class="form-row form-row-last">
<label for="' . esc_attr($this->id) . '-card-cvc">' . __('Card Code', 'woocommerce') . ' <span class="required">*</span></label>
<' . $input_type . ' id="' . esc_attr($this->id) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__('CVC', 'woocommerce') . '" name="' . ($args['fields_have_names'] ? $this->id . '-card-cvc' : '') . '"></' . $input_type . '>
</p>'
);
$fields = wp_parse_args($fields, apply_filters('woocommerce_credit_card_form_fields', $default_fields, $this->id));
?>
<fieldset id="<?php echo $this->id; ?>-cc-form">
<?php do_action( 'woocommerce_credit_card_form_start', $this->id ); ?>
<?php
foreach ($fields as $field) {
echo $field;
}
?>
<?php do_action( 'woocommerce_credit_card_form_end', $this->id ); ?>
<div class="clear"></div>
</fieldset>
<?php
}
private function _check_gateway_settings()
{
if (empty($this->_merchant_id) || empty($this->_public_key) || empty($this->_private_key)) {
$cause = array();
empty($this->_merchant_id) && $cause[] = sprintf(__('%s Merchant ID', $this->id), self::VENDOR);
empty($this->_public_key) && $cause[] = __('Public Key', $this->id);
empty($this->_private_key) && $cause[] = __('Private Key', $this->id);
$cause = implode(',', $cause);
$message = sprintf(__('The payment gateway does not work without %s %s.', $this->id), '<strong>' . $this->get_environment_name() . '</strong>', $cause);
$this->alt_admin_notice($message, 'error', sprintf(__('The %s %s is not specified.', $this->id), self::VENDOR, $cause));
return false;
}
return true;
}
public function get_method_description($description_details = '')
{
$plugin_data = get_plugin_data($this->get_plugin_mainfile());
$description_buttons = array();
https: // sandbox.braintreegateway.com/login?_ga=1.149214390.145895843.1442829042
$description_div = "<div id='get-started' class='woo-mynix-braintree-gateway'><table><tr><td><img src='%s'></td><td>%s</td></tr></table></div>";
$description_main = '<span class="main">' . sprintf(__('Get started with %s', $this->id), $plugin_data['Name']) . '</span>';
$keys_prefix = $this->_sandbox ? 'sandbox_' : '';
$braintree_login_gateway = self::VENDOR_GATEWAY;
if (empty($this->_merchant_id))
$description_buttons[] = get_anchor('http://www.' . self::VENDOR_URL . '/signup', __('Sign Up Now', $this->id), 'button button-primary');
else
$description_buttons[] = get_anchor(sprintf('http://%s.%s/login', $this->_sandbox ? 'sandbox' : 'www', $braintree_login_gateway), sprintf(__('Login in %s', $this->id), $this->get_environment_name()), 'button');
empty($description_details) && $description_details = sprintf(__('%s provides a fully PCI Compliant, secure way to collect and transmit credit card data to %s payment gateway while keeping you in control of your web site design.', $this->id), $plugin_data['Name'], self::VENDOR, get_anchor('http://www.' . self::VENDOR_URL, self::VENDOR));
return sprintf($description_div, plugins_url('assets/img/icon-128x128.png', __DIR__), $description_main . '<span>' . $description_details . '</span><p>' . implode('', $description_buttons) . '</p>');
}
protected function _init()
{
$this->VENDOR_DOCS_URL = 'articles.' . self::VENDOR_URL;
$this->VENDOR_FAQ_URL = "http://{$this->VENDOR_DOCS_URL}/get-started/";
$this->VENDOR_CPANEL_URL = "http://{$this->VENDOR_DOCS_URL}/control-panel/";
$this->VENDOR_DEVELOPER_URL = "http://developers." . self::VENDOR_URL . "/ios+php/";
$this->method_title = __(self::VENDOR, $this->id);
$this->title = $this->method_title;
$this->icon = null;
$this->braintree_badge = 'yes';
$this->badge_icon = self::VENDOR_BADGE_ICON;
$this->has_fields = false;
if (isset($_POST['woocommerce_' . $this->id . '_reset'])) {
update_option($this->plugin_id . $this->id . '_settings', null);
}
$this->init_settings();
empty($this->settings) && $this->settings = get_option($this->plugin_id . $this->id . '_settings', null);
$checkboxes = array(
'sandbox',
'enabled'
);
foreach ($checkboxes as $key) {
$prop = "_$key";
$this->$prop = isset($this->settings[$key]) && ('yes' == $this->settings[$key]);
}
$keys_prefix = $this->_sandbox ? 'sandbox_' : '';
$dynamic_keys = array(
'public_key',
'private_key',
'merchant_id'
);
foreach ($dynamic_keys as $key) {
$prop = "_$key";
isset($this->settings[$keys_prefix . $key]) && ($this->$prop = $this->settings[$keys_prefix . $key]) || $this->$prop = '';
}
$complex_fields = array();
foreach ($this->get_mynix_form_fields() as $field_group => $form_fields)
foreach ($form_fields as $field_key => $field_def)
isset($field_def['type']) && in_array($field_def['type'], array(
'hidden',
'complex'
)) && $complex_fields[] = $field_key;
if (is_array($this->settings)) {
foreach ($this->settings as $setting_key => $value)
if (! in_array($setting_key, $dynamic_keys)) {
if (in_array($setting_key, $complex_fields)) {
$item = $this->settings[$setting_key];
if (is_array($item)) {
$item = array();
foreach ($this->settings[$setting_key] as $k => $v)
$item[html_entity_decode($k)] = html_entity_decode($v);
} else {
$item = html_entity_decode($item);
}
$this->$setting_key = $item;
} else {
$this->$setting_key = $this->settings[$setting_key];
}
}
}
$this->method_description = $this->get_method_description(  );
if (! method_exists($this, 'get_form_fields'))
$this->form_fields = $this->settings_api_form_fields();
}
public function __construct()
{
parent::__construct();
$this->id = preg_replace('/.*\\\\(.*)/', '$1', __CLASS__);
$this->id = str_replace('_', '-', strtolower($this->id));
$this->method_description = '';
$this->_init();
$this->payment_handler = new MynixBraintreeHandler($this);
$hooks = array(
'admin_notices' => 'check_environment',
'woocommerce_update_options_payment_gateways_' . $this->id => 'process_admin_options',
'woocommerce_update_options_payment_gateways' => 'process_admin_options', 
'woocommerce_credit_card_form_start' => 'before_credit_card_form',
'woocommerce_credit_card_form_end' => 'after_credit_card_form',
'woocommerce_credit_card_form_fields' => 'override_credit_card_form',
'woocommerce_order_button_html' => 'override_place_order_button',
'woocommerce_review_order_before_submit' => 'place_order_before_submit',
'woocommerce_review_order_after_submit' => 'place_order_after_submit',
'woocommerce_settings_api_form_fields_' . $this->id => 'settings_api_form_fields'
);
! $this->_option_enabled('debug_mode') && $hooks['wp_head'] = 'on_wp_head';
$filters = array(
'admin_footer_text' => 'on_admin_get_footer_text'
);
foreach ($hooks as $hook => $hook_handler)
add_action($hook, array(
&$this,
is_string($hook_handler) ? $hook_handler : $hook_handler[0]
), is_array($hook_handler) ? $hook_handler[1] : 10);
foreach ($filters as $filter => $filter_handler)
add_filter($filter, array(
&$this,
is_string($filter_handler) ? $filter_handler : $filter_handler[0]
), is_array($filter_handler) ? $filter_handler[1] : 10, is_array($filter_handler) ? $filter_handler[2] : 1);
}
protected function payment_failed($order, $errors)
{
is_array($errors) || $errors = array(
$errors
);
$error_message = ! empty($errors) ? implode(PHP_EOL, $errors) : __('unknown', $this->id);
$order->add_order_note(sprintf(__('%s payment failed with message: "%s"', $this->id), self::VENDOR, get_anchor($this->VENDOR_DEVELOPER_URL . 'reference/general/processor-responses/authorization-responses', $error_message)));
$order->update_status('failed', __('Failed', $this->id));
$this->add_notice($error_message, 'error');
$this->log($error_message, 'error');
}
protected function order_complete($order, $transaction_id = '')
{
if ($order->status == 'completed') {
$this->log(__('Order already completed. This should never happen.', 'warning', $this->id));
return;
}
$order->payment_complete($transaction_id);
$auto_settled = $this->_option_enabled('submit_settlement');
$order->add_order_note(sprintf(__('%s payment completed (txid: %s)%s.', $this->id), self::VENDOR, $transaction_id, $auto_settled ? '' : sprintf(__(' (but not %s)', $this->id), get_anchor($this->VENDOR_FAQ_URL . 'transaction-life-cycle"', __('submitted for settlement', $this->id)))));
global $woocommerce;
$woocommerce->cart->empty_cart(true);
if (isset($_SESSION))
unset($_SESSION['order_awaiting_payment']);
$this->log(sprintf(__('Order #%s completed (payment completed/stock reduced/cart emptied)', $this->id), $order->id), 'notice');
}
protected function _get_prefixes($sandbox = null)
{
$sandbox = (! isset($sandbox) && $this->_sandbox) || $sandbox;
$key_prefix = $sandbox ? 'sandbox_' : '';
$environment = $this->get_environment_name($sandbox);
$help_prefix = self::VENDOR . ' ' . get_anchor($this->VENDOR_FAQ_URL . 'try-it-out#switching-from-sandbox-to-production', $environment);
return array(
$key_prefix,
$help_prefix,
$environment
);
}
protected function get_integration_code($integration_type = false)
{
return 'custom';
}
protected function get_integration_types()
{
return array(
self::GROUP_OPTION_CUSTOM => __('Custom UI', $this->id)
);
}
protected function get_settings_groups_description($group_id = null)
{
$integration_types = $this->get_integration_types();
$descriptions = parent::get_settings_groups_description() + array(
self::GROUP_OPTION_COMMON => __('Common options that aids in card checkout form customization', $this->id),
self::GROUP_OPTION_CUSTOM => sprintf(__('Allows various customization of the %s fields on the checkout form', $this->id), $integration_types[self::GROUP_OPTION_CUSTOM]),
self::GROUP_OPTION_PAYPAL => sprintf(__('Allows customization of % PayPal button on the checkout form', $this->id), self::VENDOR)
);
return isset($descriptions[$group_id]) ? $descriptions[$group_id] : $descriptions;
}
protected function get_settings_groups()
{
$groups = parent::get_settings_groups() + array(
self::GROUP_OPTION_COMMON => __('Checkout', $this->id),
self::GROUP_OPTION_PAYPAL => __('PayPal', $this->id)
) + $this->get_integration_types();
ksort($groups);
return $groups;
}
protected function get_mynix_form_fields()
{
$wc_version = @constant('WC_VERSION') ? WC_VERSION : (constant('WOOCOMMERCE_VERSION') ? WOOCOMMERCE_VERSION : '0.0');
$fallback = version_compare($wc_version, '2.1', '<');
$description_default = sprintf(__('Pay securely using your credit and debit card via %s - a PayPal company', $this->id), get_anchor('http://en.wikipedia.org/wiki/Braintree_%28company%29', self::VENDOR));
$title = array(
'title' => __('Title', $this->id),
'type' => 'text',
'desc_tip' => __('The name of payment method the customer sees on the checkout form.', $this->id),
'default' => __('Credit card', $this->id)
);
$fallback && $title['description'] = $title['desc_tip'];
$description = array(
'title' => __('Description', $this->id),
'type' => 'textarea',
'desc_tip' => __('Payment method description the customer sees on the checkout form by choosing this method.', $this->id),
'default' => $description_default,
'description' => __('Preview', $this->id) . ' : <code>' . (isset($this->description) ? html_entity_decode($this->description) : $description_default) . '</code>',
'css' => 'max-width:350px;'
);
$sandbox = array(
'title' => sprintf(__('%s Sandbox Mode', $this->id), self::VENDOR),
'label' => __('Enable Sandbox Mode', $this->id),
'type' => 'checkbox',
'desc_tip' => __('Turn the payment gateway in sandbox mode otherwise it works in production environment.', $this->id),
'default' => 'yes'
);
$fallback && $sandbox['description'] = $sandbox['desc_tip'];
$form_fields = array(
'enabled' => array(
'title' => __('Enable / Disable', $this->id),
'label' => __('Enable this payment gateway', $this->id),
'type' => 'checkbox',
'default' => 'no'
),
'title' => $title,
'description' => $description,
'sandbox' => $sandbox
);
foreach (array(
false,
true
) as $sandbox) {
list ($key_prefix, $help_prefix, $environment) = $this->_get_prefixes($sandbox);
$field_type = ($this->_sandbox && 'sandbox_' != $key_prefix) || (! $this->_sandbox && 'sandbox_' == $key_prefix) ? 'hidden' : 'text';
$field_type = $this->_enabled ? $field_type : 'hidden';
$form_fields = array_merge($form_fields, array(
$key_prefix . 'merchant_id' => array(
'title' => sprintf(__('%s Merchant ID', $this->id), self::VENDOR),
'type' => $field_type,
'description' => sprintf(__('%s specific %s%s.', $this->id), $help_prefix, get_anchor($this->VENDOR_CPANEL_URL . 'important-gateway-credentials#merchant-account-id-vs.-merchant-id', __('merchant ID', $this->id)), defined(__NAMESPACE__.'\\MYNIX_BRAINTREE_ENHANCED') ? '' : (' ' . __('using its default currency', $this->id))),
'default' => '',
'desc_tip' => false,
'placeholder' => sprintf(__('Your %s %s merchant ID', $this->id), self::VENDOR, strtoupper($environment))
),
$key_prefix . 'public_key' => array(
'title' => __('Public Key', $this->id),
'type' => $field_type,
'description' => sprintf(__('%s specific %s identifier for SSL encryption.', $this->id), $help_prefix, get_anchor($this->VENDOR_CPANEL_URL . 'important-gateway-credentials', __('public', $this->id))),
'default' => '',
'desc_tip' => false,
'placeholder' => sprintf(__('Your %s %s public key', $this->id), self::VENDOR, strtoupper($environment))
),
$key_prefix . 'private_key' => array(
'title' => __('Private Key', $this->id),
'type' => $field_type,
'description' => sprintf(__('%s specific %s identifier that %s', $this->id), $help_prefix, get_anchor($this->VENDOR_CPANEL_URL . 'important-gateway-credentials', __('secure', $this->id)), '<span style="color:#DD3D36">' . __('should not be shared with ANYONE!', $this->id) . '</span>'),
'default' => '',
'desc_tip' => false,
'placeholder' => sprintf(__('Your %s %s private key', $this->id), self::VENDOR, strtoupper($environment))
)
));
}
$field_type = $this->_enabled && ! (empty($this->_private_key) || empty($this->_public_key) || empty($this->_merchant_id)) ? 'button' : 'hidden';
if ($this->_enabled && ! (empty($this->_private_key) || empty($this->_public_key) || empty($this->_merchant_id))) {
$form_fields = array_merge($form_fields, array(
'test_button' => array(
'type' => $field_type,
'desc_tip' => __('Test the connection to the Braintree server against specified IDs/keys.', $this->id),
'description' => sprintf('<span id="%s-test_btn"></span>', $this->id),
'default' => __('Test connection', $this->id),
'css' => "width:auto;cursor:pointer",
'class' => 'button-primary mynix-button-primary',
'custom_attributes' => array(
'onclick' => "document.body.style.cursor='wait';jQuery.post(ajaxurl,{'action':'mynix_braintree_test_connection','mynix_nonce':'" . wp_create_nonce('mynix-client-token-nonce') . "'},function(a){var c='red',m=a,d=document.getElementById('" . sprintf('%s-test_btn', $this->id) . "');try{var b=JSON.parse(a);if(d)if('undefined'==typeof b.error){c='green';m=b.success;}else {m=b.error;}}catch(e){m= jQuery('<div/>').html(e.message).text()+' (see Console)';console && console.log(a);}d.style.color=c;d.innerHTML=m;document.body.style.cursor='default';});"
)
)
));
}
$form_fields['integration_type'] = array(
'title' => __('UI integration type', $this->id),
'type' => 'select',
'desc_tip' => implode('<br>', array(
__('Select the UI type that fits your PCI-DSS requirements:', $this->id),
__('Hosted Fields', $this->id) . ' => SAQ A',
__('Drop-in UI', $this->id) . ' => SAQ A',
__('Custom fields', $this->id) . ' => SAQ A-EP'
)),
'description' => sprintf(__('Select how you want to integrate the checkout form with %s: %s | %s | %s', $this->id), self::VENDOR, __('Custom UI', $this->id), get_anchor('http://' . self::VENDOR_URL . '/features/hosted-fields', __('Hosted fields')), get_anchor('http://' . self::VENDOR_URL . '/features/drop-in', __('Drop-in UI'))),
'default' => self::GROUP_OPTION_CUSTOM,
'options' => $this->get_integration_types()
);
$result[self::GROUP_OPTION_DEFAULT] = $form_fields + current(parent::get_mynix_form_fields());
$suported_cards = $this->get_supported_cards(true);
$icons_positions = array(
'icons_top' => __('Icons near payment method title', $this->id),
'icons_bottom' => __('Icons under payment method description', $this->id)
);
$result[self::GROUP_OPTION_COMMON] = array(
'display_accepted_cards' => array(
'title' => __('Display cards icons', $this->id),
'type' => 'checkbox',
'label' => __('Display the accepted cards icons', $this->id),
'description' => __('Preview', $this->id) . ' : ' . $this->_get_card_icons(),
'default' => 'yes',
'desc_tip' => __('The icons shown near card form when this payment method is chosen in the checkout page', $this->id)
),
'icons_position' => array(
'title' => __('Card icons position', $this->id),
'type' => $this->_option_enabled('braintree_badge') || $this->_option_enabled('display_accepted_cards') ? 'select' : 'hidden_select',
'description' => __('Select the location where the card icons will be positioned within the checkout card form', $this->id),
'default' => '',
'options' => $icons_positions,
'desc_tip' => sprintf(__('If you choose the `%s` then make sure they fit there', $this->id), $icons_positions['icons_top'])
),
'accepted_cards' => array(
'title' => __('Accepted Cards', $this->id),
'type' => 'multiselect',
'options' => $suported_cards,
'desc_tip' => __('What cards providers (ie. Visa, Mastercard) you want to accept with this gateway. Requires Card validation ON.', $this->id),
'default' => array_keys($suported_cards),
'description' => sprintf(__('Select the types of %s supported by your webshop.', $this->id), get_anchor('http://' . $this->VENDOR_DOCS_URL . '/wells-flat/transactions/accepted-payment-methods', __('the cards', $this->id))),
'class' => 'woocommerce-select'
),
'pci_badge' => array(
'title' => 'PCI Compliance badge URL',
'type' => 'inputbutton',
'description' => __('Preview', $this->id) . ' : ' . $this->_get_pci_badge(),
'default' => $this->_get_pci_badge_url(true),
'desc_tip' => __('Hide the badge by using an empty URL', $this->id),
'button' => array(
'value' => __('Default', $this->id),
'onclick' => "jQuery('#" . $this->plugin_id . $this->id . "_pci_badge').val('" . $this->_get_pci_badge_url(true) . "');"
)
),
'pci_text' => array(
'title' => __('PCI compliance description', $this->id),
'type' => 'text',
'description' => __('A text that appears near the PCI badge, if the badge is defined', $this->id),
'default' => $this->_get_pci_badge_text(true),
'desc_tip' => __('Hide the description by using an empty text', $this->id)
),
'checkout_ui_block' => array(
'title' => __('Block UI on order submit', $this->id),
'type' => 'checkbox',
'description' => __('Adds a blockin DIV on the checkout form while submitting the order', $this->id),
'default' => 'yes',
'desc_tip' => __('Disable this option if your store theme already provides this feature', $this->id)
),
'checkout_ui_block_caption' => array(
'title' => __('Block UI caption', $this->id),
'type' => 'text',
'description' => __('The `please wait` message shown on on the blocking layer while placing the order', $this->id),
'default' => htmlspecialchars(__('Processing payment.<br>Please wait...'))
)
);
$result[self::GROUP_OPTION_PAYPAL] = array(
'paypal_enabled' => array(
'title' => __('Enable PayPal button', $this->id),
'type' => 'checkbox',
'description' => sprintf(__('Display a %s PayPal button above the card checkout form', $this->id), self::VENDOR),
'default' => 'no',
'desc_tip' => __('Enable this only if the WooCommerce built-in PayPal payment method doesn\'t work for you', $this->id)
),
'paypal_button_title' => array(
'title' => __('PayPal button field label', $this->id),
'type' => 'text',
'default' => $this->_get_paypal_button_title(true),
'description' => __('This label should tell your customer that he/she is going to pay via PayPal', $this->id),
'desc_tip' => __('Make sure that the text here fits into your layout', $this->id)
),
'paypal_opacity' => array(
'title' => __('PayPal opacity effect', $this->id),
'type' => 'checkbox',
'default' => 'yes',
'description' => __('The opacity displays PayPal button merely as a second choice', $this->id),
'label' => __('Apply an opacity CSS transition effect', $this->id),
'desc_tip' => __('Keep your customers as long as possible on your site', $this->id)
)
);
return $result;
}
public function get_payload($order, $ignore_order_items = null)
{
$payment_method_field = 'payment_method_nonce';
if (! isset($_REQUEST[$payment_method_field])) {
$admin_email = get_bloginfo('admin_email');
$message = 'The following request received without `' . $payment_method_field . '` field. This should never happen!<br>Try to clear your browser\'s cache and see if that helps.<br>';
$message .= '<pre>' . print_r($_REQUEST, 1) . '</pre>';
$message .= 'Source: ' . __FILE__ . ':' . __LINE__;
! empty($admin_email) && wp_mail($admin_email, $payment_method_field . ' issue', $message, "Importance:high\r\nContent-Type: text/html\r\n");
return array(
'error' => __('Your request contains invalid data. This should never happen. An e-mail was sent to our staff to investigate the issue. We are sorry for this inconvenient :-(', $this->id)
);
}
$customer = array(
'firstName' => $order->billing_first_name,
'lastName' => $order->billing_last_name
);
$phone = preg_replace('/[\D]/', '', $order->billing_phone);
strlen($phone) > 9 && strlen($phone) < 15 || $phone = null;
! empty($order->billing_company) && $customer['company'] = $order->billing_company;
! empty($phone) && $customer['phone'] = $order->billing_phone;
! empty($order->billing_email) && $customer['email'] = $order->billing_email;
$billing = array(
'firstName' => $customer['firstName'],
'lastName' => $customer['lastName'],
'streetAddress' => $order->billing_address_1,
'extendedAddress' => $order->billing_address_2,
'locality' => $order->billing_city,
'region' => $order->billing_state,
'postalCode' => $order->billing_postcode,
'countryCodeAlpha2' => $order->billing_country
);
isset($customer['company']) && $billing['company'] = $customer['company'];
if (is_array($ignore_order_items) && $this->_option_enabled('recurring_subs_order_lines')) {
foreach ($order->get_items() as $order_item_id => $order_item)
if (in_array($order_item_id, $ignore_order_items)) {
$order->update_fee($order_item_id, array(
'line_total' => 0,
'line_tax' => 0
));
}
$order->calculate_totals();
}
$order_amount = $this->get_order_payable_amount($order);
$payload = array(
'amount' => sprintf('%.2f', $order_amount),
'orderId' => str_replace("#", "", $order->get_order_number()),
'paymentMethodNonce' => $_REQUEST[$payment_method_field],
'customer' => $customer,
'billing' => $billing,
'shipping' => $billing
);
return $payload;
}
public function check_environment()
{
$this->_init();
if (isset($_POST[$this->plugin_id . $this->id . '_reset'])) {
$this->add_notice(sprintf(__('IMPORTANT : %s settings have just been reset to their factory defaults. Please set them as needed.', $this->id), self::VENDOR), 'update-nag');
}
if (! $this->_enabled) {
$this->log(self::VENDOR . ' Payment Gateway not enabled', 'notice');
return false;
}
$this->_check_gateway_settings();
if ('no' == get_option('woocommerce_force_ssl_checkout') || ! is_ssl() )  {
$this->add_notice(sprintf(__('%s Payment Gateway is enabled, but the %s is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate.', $this->id), self::VENDOR, get_anchor(get_wc_settings_section_link(), __('force SSL option', $this->id), false, false, '_self')), 'update-nag');
}
return true;
}
public function generate_badge_html($key, $data)
{
return preg_replace(array(
'/(<input)([^>]+>)/i',
'/type="badge"/'
), array(
self::VENDOR_BADGE_URI . '\1\2 ' . '<input type="button" class="button" value="' . __('Default', $this->id) . '" onclick="jQuery(\'#' . $this->plugin_id . $this->id . '_badge_icon\').val(\'' . self::VENDOR_BADGE_ICON . '\')"/>&nbsp;' . get_anchor('http://www.' . self::VENDOR_URL . '/badge', __('Read more', $this->id), 'help_tip', 'cursor:help', '_blank', 'data-tip="' . sprintf(__('Check other badgeds on %s website', $this->id), self::VENDOR) . '"'),
'type="text"'
), $this->generate_text_html($key, $data));
}
public function admin_options()
{
$this->init_settings();
parent::admin_options();
if (! defined(__NAMESPACE__.'\\MYNIX_BRAINTREE_ENHANCED')) {
$shop_url = APP_ADDONS_SHOP_URI;
$pro_feat_list = $shop_url . 'woo-mynix-braintree-full-features-list/';
$pro_features = array(
array(
$this->VENDOR_DEVELOPER_URL . 'guides/3d-secure/overview' => __('3D-Secure', $this->id)
),
array(
'http://' . self::VENDOR_URL . '/features/drop-in' => __('Drop-in UI integration', $this->id)
),
array(
'http://' . self::VENDOR_URL . '/features/hosted-fields' => __('Hosted Fields integration', $this->id)
),
array(
$pro_feat_list . '#woo_payment_threshold_non_3ds' => __('payment risk management', $this->id)
),
array(
$this->VENDOR_FAQ_URL . 'transaction-life-cycle' => __('submitting transactions for settlement automatically', $this->id)
),
array(
$this->VENDOR_FAQ_URL . 'payment-methods-currencies#handling-payments-in-multiple-currencies' => __('handling payments in multiple currencies', $this->id)
),
array(
$this->VENDOR_CPANEL_URL . 'transactions/descriptors' => __('bank statement descriptors', $this->id)
),
array(
$this->VENDOR_CPANEL_URL . 'recurring-billing/overview' => __('recurring billings', $this->id),
'enabled' => true
),
array(
$this->VENDOR_CPANEL_URL . 'transactions/refunds-voids-credits' => __('refunds', $this->id)
),
array(
$pro_feat_list . '#woopayment-custom' => __('customization of the checkout card form', $this->id)
),
array(
$pro_feat_list . '#woo_ccv_verification' => __('disabling the Card Code Validation (CCV)', $this->id)
),
array(
$pro_feat_list . '#woo_accepted_cards' => __('toggling the accepted cards', $this->id)
),
array(
$pro_feat_list . '#woo_debug_mode' => __('debugging the payment transactions', $this->id)
),
__('localized messages', $this->id),
array(
APP_ADDONS_SHOP_URI . 'shop/premium-support/' => __('premium support', $this->id)
)
);
$features = array();
foreach ($pro_features as $feature) {
if (is_array($feature)) {
$enabled = ! isset($feature['enabled']) || $feature['enabled'];
$features[] = get_anchor(key($feature), current($feature), false, $enabled ? false : 'text-decoration:line-through;');
} else
$features[] = $feature;
}
$pro_url = $shop_url . 'woo-mynix-braintree-pro';
$this->alt_admin_notice(sprintf(__('Do you need advanced features such as %s? If you do then upgrade to the %s. %s | %s | %s', $this->id), implode(', ', $features), get_anchor($pro_url, __('PRO version', $this->id), false, 'color:#FF5950;font-weight:bold;'), get_anchor($shop_url . 'woo-mynix-braintree-comparison', __('Free vs PRO', $this->id), false, 'color:#FF5950;font-weight:bold;'), get_anchor($shop_url . 'woo-mynix-braintree-full-features-list', __('30+ Full feature list', $this->id), false, 'color:#FF5950;font-weight:bold;'), get_anchor($shop_url . 'woo-mynix-braintree-screenshots', __('40+ PRO Screenshots', $this->id), false, 'color:#FF5950;font-weight:bold;')), 'update-nag', false, array(
plugins_url('assets/img/pro-icon.png', __DIR__),
$pro_url
));
}
}
public function is_available()
{
if (! $this->_enabled) {
return false;
}
if (! (is_ssl() || $this->_sandbox || get_option('woocommerce_force_ssl_checkout') == "no")) {
$this->log(self::VENDOR . ' gateway not available due to no SSL + not Sandbox environment + SSL Force Checkout=yes', 'notice');
return false;
}
$is_available = ! (empty($this->_merchant_id) || empty($this->_public_key) || empty($this->_private_key));
if (! $is_available) {
$this->log(self::VENDOR . ' gateway not available due to no Merchant ID or SSL keys', 'notice');
}
return $is_available && parent::is_available();
}
protected function _get_badge_icon()
{
if ($this->_option_enabled('braintree_badge')) {
$tip = strip_tags($this->description);
return get_anchor('http://www.' . self::VENDOR_GATEWAY . '/merchants/' . $this->_merchant_id . '/verified', sprintf('<img src="%s" alt="%s" data-tip="%s" title="%s" class="help_tip" style="height:24px"/>', self::VENDOR_BADGE_URI . (isset($this->badge_icon) && ! empty($this->badge_icon) ? $this->badge_icon : self::VENDOR_BADGE_ICON), self::VENDOR, $tip, $tip), false, 'float:right;margin-top:5px;margin-bottom:5px;');
}
return '';
}
private function _get_paypal_button_title($default = false)
{
$default_title = __('PayPal instead card?', $this->id);
return $default || ! isset($this->paypal_default_title) ? $default_title : $this->paypal_default_title;
}
public function get_description()
{
$result = html_entity_decode($this->description);
if ($this->_sandbox) {
$result .= sprintf('<p style="text-align:center">%s%s</p>', get_anchor($this->VENDOR_FAQ_URL . 'try-it-out', __('SANDBOX ENABLED', $this->id)), get_help_tooltip(__('When Braintree Sandbox is enabled all the payments are only simulated (they are not real).', $this->id)));
}
return $result . '<div style="display:block;width:100%">' . $this->_get_badge() . '</div>';
}
public function payment_fields()
{
parent::payment_fields();
if (! $this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM))
return;
elseif (! method_exists($this, 'credit_card_form'))
$this->_fallback_credit_card_form();
echo $this->_get_pci_badge();
}
protected function get_custom_ui_scripts($suffix = '.min')
{
$result = array();
$wp_deps = array(
'jquery',
'jquery-effects-shake',
'wc-checkout',
$this->id . '-sdk'
);
global $woocommerce;
$wc_path = $woocommerce->plugin_path();
$wc_deps = array(
'jquery-tiptip' => '/assets/js/jquery-tiptip/jquery.tipTip'
);
$this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM) && $wc_deps['wc-credit-card-form'] = '/assets/js/frontend/credit-card-form';
foreach ($wc_deps as $handle => $wc_deps_relpath) {
$script_relpath = $wc_path . $wc_deps_relpath . $suffix . '.js';
$files = glob($wc_path . $wc_deps_relpath . '*.js');
if (false !== $files && wp_script_is($handle, 'registered')) {
continue;
} else {
if (empty($files)) {
unset($wc_deps[$handle]);
} else {
if (in_array($script_relpath, $files)) {
$result[$handle] = array(
'src' => $woocommerce->plugin_url() . $wc_deps_relpath . $suffix . '.js',
'deps' => array(
'jquery'
)
);
} else {
$wc_deps[$handle . '-surogate'] = preg_replace('/^' . preg_quote($wc_path, '/') . '/', '', $files[0]);
}
}
}
}
$plugin_script = 'assets/js/' . $this->id;
file_exists(plugin_dir_path(__DIR__) . $plugin_script . $suffix . '.js') && $plugin_script .= $suffix;
$result[$this->id] = array(
'src' => plugins_url($plugin_script . '.js', __DIR__),
'deps' => array_merge($wp_deps, array_keys($wc_deps)),
'footer' => true
);
if ($this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM)) {
$plugin_script = 'assets/js/' . $this->id . '-custom';
file_exists(plugin_dir_path(__DIR__) . $plugin_script . $suffix . '.js') && $plugin_script .= $suffix;
$result[$this->id . '-custom'] = array(
'src' => plugins_url($plugin_script . '.js', __DIR__),
'deps' => array(
$this->id
),
'footer' => true
);
}
return $result;
}
protected function get_scripts($suffix = '.min')
{
global $woocommerce;
$result = parent::get_scripts($suffix);
$result = $result + $this->get_custom_ui_scripts($suffix);
$result[$this->id . '-sdk'] = array(
'src' => 'https://js.' . self::VENDOR_GATEWAY . '/v2/braintree.js',
'deps' => array(
'jquery'
),
'footer' => true
);
$has_jquery_tiptip = false;
foreach ($result as $handle => $script) {
if (isset($script['deps']) && in_array('jquery-tiptip', $script['deps'])) {
$has_jquery_tiptip = true;
break;
}
}
$has_jquery_tiptip || $result['jquery-tiptip-surogate'] = array(
'src' => 'https://raw.githubusercontent.com/drewwilson/TipTip/master/jquery.tipTip.js',
'deps' => array(
'jquery'
),
'footer' => false
);
$plugin_script = 'assets/js/' . $this->id . '-paypal';
file_exists(plugin_dir_path(__DIR__) . $plugin_script . $suffix . '.js') && $plugin_script .= $suffix;
$this->_option_enabled('paypal_enabled') && $result[$this->id . '-paypal'] = array(
'src' => plugins_url($plugin_script . '.js', __DIR__),
'deps' => array(
'jquery',
$this->id
),
'footer' => true
);
return $result;
}
protected function enqueue_params()
{
$wc_mynix_params = parent::enqueue_params();
$wc_mynix_params = $wc_mynix_params + array(
'ajaxurl' => admin_url('admin-ajax.php'),
'mynix_nonce' => wp_create_nonce('mynix-client-token-nonce'),
'integration_type' => $this->get_integration_code(),
'integration_container' => $this->id . '-' . $this->_braintree_container
);
if ($this->_option_enabled('paypal_enabled')) {
$wc_mynix_params['paypal_displayName'] = isset($this->paypal_merchant_name) ? $this->paypal_merchant_name : '';
$this->_option_enabled('paypal_locale', '') && $wc_mynix_params['paypal_locale'] = $this->paypal_locale;
$wc_mynix_params['paypal_opacity'] = $this->_option_enabled('paypal_opacity');
}
if (! $this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM) || $this->_option_enabled('paypal_enabled')) {
$wc_mynix_params['currency'] = $this->get_order_currency();
}
return $wc_mynix_params;
}
public function process_payment($order_id)
{
if (! $this->is_available()) {
$this->log('Payment cannot be processed either due to ' . self::VENDOR . ' payment not enabled or its SDK initialization error', 'notice');
return false;
}
try {
$order = new \WC_Order($order_id);
$response = $this->payment_handler->sale($order);
if (isset($response['txid'])) {
$this->log(sprintf('Payment succeeded. Setting order #%s status => Complete', $order_id), 'notice');
$this->order_complete($order, $response['txid']);
return array(
'result' => 'success',
'redirect' => $this->get_return_url($order)
);
} elseif (isset($response['errors'])) {
$this->payment_failed($order, $response['errors']);
}
} catch (\Exception $e) {
$this->add_notice($e->getMessage(), 'error');
}
return array(
'result' => 'fail',
'redirect' => ''
);
}
public function before_credit_card_form()
{
if ($this->_option_enabled('paypal_enabled')) {
$id = $this->id . '-' . $this->_paypal_container;
$class = $id . '-wrapper';
! $this->_option_enabled('paypal_opacity') && $class .= ' has-paypal';
printf('<div class="%s"><label style="display:none">%s</label><div id="%s"></div></div>', $class, $this->_get_paypal_button_title(), $id);
}
printf('<div id="%s-%s">', $this->id, $this->_braintree_container);
}
public function after_credit_card_form()
{
echo '</div>';
}
public function override_credit_card_form($fields)
{
$data_names = array(
'card-number-field' => array(
'number',
20,
'text'
),
'card-expiry-field' => array(
'expiration_date',
9,
'text'
),
'card-cvc-field' => array(
'cvv',
4,
'number'
)
);
$fields_have_names = false;
$pattern = $fields_have_names ? '/(\<\binput\b )/' : '/(\<\binput\b[^>]+) name=([\'"]).*?\2(.*>)/';
foreach ($data_names as $key => $value) {
if (isset($fields[$key])) {
$maxlenstr = '';
$field_type = $value[2];
$maxlen = $value[1];
$data_name = $value[0];
$maxlenstr = false === strpos($fields[$key], 'maxlength') ? sprintf(' maxlength="%d"', $maxlen) : '';
$fields[$key] = preg_replace($pattern, '$1 data-braintree-name="' . $data_name . '"' . $maxlenstr . ' $3', $fields[$key]);
$classes = array(
$this->id . '-card-field'
);
$fields[$key] = preg_replace('/(\<\binput\b[^>]+class=([\'"]))(.*?)(\2.*)/', '$1$3 ' . implode(' ', $classes) . '$4', $fields[$key]);
$fields[$key] = preg_replace('/type=(["\'])text\1/', 'type=$1' . $field_type . '$1', $fields[$key]);
}
}
return $fields;
}
public function override_place_order_button($html)
{
if (! $this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM)) {
return $html;
}
$onclick = preg_match('/onclick\s*=/', $html) ? '' : ' onclick=$4jQuery(&quot;form.checkout, form#order_review&quot;).submit();$4';
$html = preg_replace('/(<\s*input[^>]*?id\s*=\s*([\'"])place_order\2[^>]*?type\s*=\s*([\'"]))[^\3]*?(\3[^>]*)/', '$1button$4' . $onclick, $html);
return preg_replace('/(<\s*input[^>]*?type\s*=\s*([\'"]))[^\2]*?(\2[^>]*?id\s*=\s*([\'"])place_order(\4[^\/>]*))/', '$1button$3' . $onclick, $html);
}
function place_order_before_submit()
{
ob_start();
}
function place_order_after_submit()
{
echo $this->override_place_order_button(ob_get_clean());
}
public function get_gateway_vendor()
{
return self::VENDOR;
}
public function get_public_key()
{
return $this->_public_key;
}
public function get_private_key()
{
return $this->_private_key;
}
public function get_merchant_id()
{
return $this->_merchant_id;
}
public function get_merchant_account_id($currency)
{
return false;
}
public function is_sandbox()
{
return $this->_sandbox;
}
public function get_environment()
{
return $this->_sandbox ? 'sandbox' : 'production';
}
public function get_environment_name($sandbox = false)
{
return $sandbox || $this->_sandbox ? __('Sandbox', $this->id) : __('Production', $this->id);
}
protected function get_braintree_init()
{
$result = array();
if (is_checkout() && $this->_option_enabled('paypal_enabled') && ! $this->_option_enabled('integration_type', self::GROUP_OPTION_DROPIN))
$result['Mynix_PayPal'] = $this->_paypal_container;
return $result;
}
private function _get_braintree_ui_footer_script()
{
$result = '';
$array = $this->get_braintree_init();
if (empty($array))
return '';
ob_start();
?>
(function ($) {
$(window).load(function () {
var DELAY_BRAINTREE_SDK=<?php echo DELAY_BRAINTREE_SDK;?>;
function bind_payment_method_listener(){
setTimeout(function(){$('input[name="payment_method"]').off('change').on('change', _init_braintree_sdk);},DELAY_BRAINTREE_SDK);
}
function _init_braintree_sdk(sender){
var item='undefined' != typeof sender.target?sender.target:sender;
if ('<?php echo $this->id;?>' == $(item).val() && $(item).is(":checked")) {
Mynix_Gateway.mynixClientGetToken(function (data) {<?php
foreach ($array as $ui_func => $container)
printf('%s.init(data.token,"%s");' . PHP_EOL, $ui_func, $this->id . '-' . $container);
?>
});
} else { <?php foreach ($array as $ui_func=>$container) printf('%s.teardown();'.PHP_EOL,$ui_func); ?>
}
$('form.checkout, form#order_review').change();
}
$('form.checkout, form#order_review').change(function (sender) {
<?php
if (! $this->_option_enabled('integration_type', self::GROUP_OPTION_CUSTOM)) {
?>
if(!('custom'==wc_mynix_params.integration_type ||
'<?php echo $this->id;?>'==sender.target.value || 
'checkout'==sender.target.name || 
'<?php echo $this->id;?>'!=$('input[name="payment_method"]:checked').val() || 
$(sender.target).hasClass('<?php echo $this->id?>-card-field'))){
$('input[name="payment_method"]:not(:checked)').first().click();
$(document.body).data('events').update_checkout[0].handler();
}
<?php
}
?>
bind_payment_method_listener();
});
setTimeout(function(){
_init_braintree_sdk($('input[name="payment_method"]:checked'));},DELAY_BRAINTREE_SDK);
});})(jQuery);
<?php
$result = ob_get_clean();
return $result;
}
public function get_wp_footer_scripts()
{
$result = parent::get_wp_footer_scripts();
if (is_checkout() && $this->is_available()) {
$result[] = $this->_get_braintree_ui_footer_script();
}
return $result;
}
}
add_action('wp_ajax_nopriv_send_client_token', __NAMESPACE__ . '\\mynix_braintree_send_client_token');
add_action('wp_ajax_send_client_token', __NAMESPACE__ . '\\mynix_braintree_send_client_token');
add_action('wp_ajax_mynix_braintree_test_connection', __NAMESPACE__ . '\\mynix_braintree_test_connection');
function mynix_braintree_test_connection()
{
$class_name = defined(__NAMESPACE__.'\\MYNIX_BRAINTREE_INTERFACE_CLASS') ? MYNIX_BRAINTREE_INTERFACE_CLASS : __NAMESPACE__ . '\\Woo_Mynix_Braintree_Gateway';
$obj = new $class_name();
$result = $obj->payment_handler->test_connection(true);
if (isset($result['error']))
$response = array(
'error' => __('Oops!... ', $obj->id) . $result['error']
);
elseif (false !== $result) {
$class = MYNIX_BRAINTREE_INTERFACE_CLASS;
$response = array(
'success' => sprintf(__('%s %s Merchant Id, public and private keys tested successfully :-)', $obj->id), $class::VENDOR, $obj->get_environment_name())
);
} else
$response = array(
'error' => __('The test did not succeed for a reason or another :-(', $obj->id) . $result['error']
);
die(json_encode($response, JSON_FORCE_OBJECT));
}
function mynix_braintree_send_client_token()
{
ob_start();
$class_name = defined(__NAMESPACE__.'\\MYNIX_BRAINTREE_INTERFACE_CLASS') ? MYNIX_BRAINTREE_INTERFACE_CLASS : __NAMESPACE__ . '\\Woo_Mynix_Braintree_Gateway';
try {
$obj = new $class_name();
$merchant_account_id = $obj->get_merchant_account_id($obj->get_order_currency());
$result = $obj->payment_handler->get_client_token(false, $merchant_account_id);
$result = json_encode($result, JSON_FORCE_OBJECT);
$output = ob_get_clean();
$obj->_option_enabled('debug_mode') && ! empty($output) && file_put_contents(__FILE__ . '.log', $output, 8);
} catch (\Exception $e) {
wp_send_json_error($e->getMessage());
}
die($result);
}
}
?>