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
 * @file    : woo-mynix-payment-gateway.php $
 * 
 * @id      : woo-mynix-payment-gateway.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

! @constant('ABSPATH') && exit();
if (class_exists('WC_Payment_Gateway_CC')) {
class WC_Payment_Gateway_Abstract extends \WC_Payment_Gateway_CC
{
}
;
} else {
class WC_Payment_Gateway_Abstract extends \WC_Payment_Gateway
{
}
;
}
class Woo_Mynix_Payment_Gateway extends WC_Payment_Gateway_Abstract
{
const GROUP_OPTION_DEFAULT = 0;
const DEFAULT_CREDIT_CARD_FORM = 'default_credit_card_form';
const AMEX = 0;
const VISA = 1;
const MASTER = 2;
const DISCOVER = 3;
const DINERS = 4;
const JCB = 5;
const MAESTRO = 6;
const PAYPAL = 7;
protected $_enabled = false;
protected $wp_footer_scripts = array();
public $id;
public $payment_handler;
protected function get_plugin_mainfile()
{
return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'woo-mynix-braintree-gateway.php';
}
protected function get_wp_admin_footer_scripts()
{
return array();
}
protected function get_wp_footer_scripts()
{
return array();
}
public function __construct()
{
$hooks = array(
'wp_footer' => array(
'on_wp_footer',
100
), 
'admin_footer' => array(
'on_wp_admin_footer',
100
),
'admin_enqueue_scripts' => 'admin_enqueue_scripts',
'wp_enqueue_scripts' => 'enqueue_scripts'
);
foreach ($hooks as $hook => $hook_handler)
add_action($hook, array(
&$this,
is_string($hook_handler) ? $hook_handler : $hook_handler[0]
), is_array($hook_handler) ? $hook_handler[1] : 10);
}
public function on_wp_head()
{
if (! $this->_option_enabled('debug_mode')) {
?>
<script type="text/javascript">
var Mynix_Console_Debug = (function () {
var a = function () {};
return {
consoleLog_onbegin: a,
consoleLog: a,
consoleLog_onend: a
};
})();
</script>
<?php
}
}
public function on_wp_footer()
{
$scripts = $this->get_wp_footer_scripts();
ksort($scripts);
if (! empty($scripts)) {
array_unshift($scripts, '<script type="text/javascript">');
$scripts[] = '</script>';
echo implode(PHP_EOL, $scripts), PHP_EOL;
}
}
public function on_wp_admin_footer()
{
$scripts = $this->get_wp_admin_footer_scripts();
ksort($scripts);
if (! empty($scripts)) {
array_unshift($scripts, '<script type="text/javascript">');
$scripts[] = '</script>';
echo implode(PHP_EOL, $scripts), PHP_EOL;
}
}
protected function get_settings_groups_description($group_id = null)
{
return array(
self::GROUP_OPTION_DEFAULT => __('General settings that gives wings and make this plug-in fly', $this->id)
);
}
protected function get_settings_groups()
{
return array(
self::GROUP_OPTION_DEFAULT => 'General'
);
}
protected function enqueue_params()
{
$result = array();
$checkout_ui_block_caption = $this->_get_option('checkout_ui_block_caption');
if ($this->_option_enabled('checkout_ui_block') && trim($checkout_ui_block_caption) != '')
$result['submit_order'] = $this->_get_option('checkout_ui_block_caption');
return $result;
}
private function _enqueue_custom_css($suffix)
{
global $woocommerce;
wp_enqueue_style($this->id . '-tiptip', 'https://raw.githubusercontent.com/drewwilson/TipTip/master/tipTip.css');
$chosen_css = '/assets/css/chosen';
file_exists($woocommerce->plugin_path() . $chosen_css . $suffix . '.css') && $chosen_css .= $suffix;
wp_enqueue_style('chosen', $woocommerce->plugin_url() . $chosen_css . '.css');
}
public function admin_enqueue_scripts()
{
global $woocommerce;
$suffix = '.min';
$this->_enqueue_custom_css($suffix);
$chosen_js = '/assets/js/chosen/chosen.jquery';
file_exists($woocommerce->plugin_path() . $chosen_js . $suffix . '.js') && $chosen_js .= $suffix;
wp_enqueue_script('chosen', $woocommerce->plugin_url() . $chosen_js . '.js', array(
'jquery'
));
$admin_handle = $this->id . '-admin';
$plugin_js = 'assets/js/' . $admin_handle;
file_exists(plugin_dir_path(__DIR__) . $plugin_js . $suffix . '.js') && $plugin_js .= $suffix;
$deps = array(
'jquery'
);
file_exists($woocommerce->plugin_path() . $chosen_js . '.js') && $deps[] = 'chosen';
wp_enqueue_script($admin_handle, plugins_url($plugin_js . '.js', __DIR__), $deps, null, false);
$plugin_css = '/assets/css/' . $admin_handle;
file_exists(plugin_dir_path(__DIR__) . $plugin_css . $suffix . '.css') && $plugin_css .= $suffix;
wp_enqueue_style($admin_handle, plugins_url($plugin_css . '.css', __DIR__));
$plugin_css = '/assets/css/' . $this->id;
file_exists(plugin_dir_path(__DIR__) . $plugin_css . $suffix . '.css') && $plugin_css .= $suffix;
wp_enqueue_style($this->id, plugins_url($plugin_css . '.css', __DIR__));
}
protected function get_scripts($suffix = '.min')
{
return array();
}
public function enqueue_scripts()
{
if (! (is_checkout() && $this->is_available())) {
return false;
}
$suffix = '.min';
$__FILE__ = strpos(__FILE__, 'xdebug') ? __DIR__ . DIRECTORY_SEPARATOR . MYNIX_BRAINTREE_INTERFACE . '.php' : __FILE__;
foreach ($this->get_scripts($suffix) as $handle => $script) {
$err_msg = array();
empty($handle) && $err_msg[] = 'empty script handle';
! isset($script['src']) && $err_msg[] = (empty($handle) ? '' : $handle) . ' script src undefined';
if (! empty($err_msg)) {
trigger_error('Bad script definition in class ' . __CLASS__ . ': ' . implode(' and ', $err_msg), E_USER_NOTICE);
continue;
}
wp_register_script($handle, $script['src'], isset($script['deps']) ? $script['deps'] : null, null, isset($script['footer']) && $script['footer']);
wp_enqueue_script($handle, $script['src'], isset($script['deps']) ? $script['deps'] : null, null, isset($script['footer']) && $script['footer']);
}
wp_localize_script($this->id, 'wc_mynix_params', $this->enqueue_params());
$this->_enqueue_custom_css($suffix);
$plugin_css = 'assets/css/' . $this->id;
file_exists(plugin_dir_path(__DIR__) . $plugin_css . $suffix . '.css') && $plugin_css .= $suffix;
wp_enqueue_style($this->id, plugins_url($plugin_css . '.css', __DIR__), false);
}
public function process_admin_options()
{
parent::process_admin_options();
}
public function is_enabled()
{
return $this->_enabled;
}
public function alt_admin_notice($message, $notice_type = 'error', $log = false, $icon_url = false)
{
if (@constant('DOING_AJAX') && DOING_AJAX)
return;
$id = uniqid(basename(__FUNCTION__));
$prefix = '';
$suffix = '';
$border_color = '';
$class = 'success' == $notice_type ? 'updated' : $notice_type;
$icon = '';
switch ($notice_type) {
case 'warning':
$border_color = '#FFBA00';
$class = 'error';
break;
}
if ($icon_url) {
$icon_click = '';
if (is_array($icon_url)) {
$icon_click = isset($icon_url[1]) ? $icon_url[1] : '';
$icon_url = isset($icon_url[0]) ? $icon_url[0] : '';
}
$icon = '<td>' . get_anchor($icon_click, '<img src="' . $icon_url . '">') . '</td>';
}
if ('update-nag' != $notice_type) {
$prefix = get_anchor(menu_page_url('wc-settings', false) . '&tab=checkout&section=' . strtolower(MYNIX_BRAINTREE_INTERFACE_CLASS), __('Payment Gateway', $this->id) . ' ' . ucfirst($notice_type), false, false, '_self') . ': ';
$suffix = '<td onclick="document.getElementById(&quot;' . $id . '&quot;).style.display=&quot;none&quot;"><button type="button" class="notice-dismiss" style="position:inherit;padding:inherit;"><span class="screen-reader-text">Dismiss this notice.</span></button></td>';
}
$style = empty($border_color) ? '' : ' style="border-color:' . $border_color . '"';
$class = 'update-nag' != $notice_type ? 'woocommerce-' . $class . ' ' . $this->plugin_id . $class . ' ' . $class : $notice_type;
echo '<div id="' . $id . '" class="' . $class . '"' . $style . '>';
echo '<table style="padding:2px;margin:6px 0;width:100%"><tr>', $icon, '<td style="width:100%">', $prefix, $message, '</td>';
echo $suffix, '</tr></table></div>';
$log && $this->log($log, $notice_type);
}
protected function _get_tooltip_html($data)
{
if (method_exists($this, 'get_tooltip_html'))
return call_user_func(array(
&$this,
'get_tooltip_html'
), $data);
global $woocommerce;
$tip = $data['desc_tip'] === true ? $data['description'] : $data['desc_tip'];
if (! empty($tip))
return '<img class="help_tip" style="float:right" data-tip="' . esc_attr($tip) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" />';
return ''; 
}
protected function _get_field_key($key)
{
if (method_exists($this, 'get_field_key'))
return call_user_func(array(
&$this,
'get_field_key'
), $key);
return $this->plugin_id . $this->id . '_' . $key;
}
protected function _get_option($key)
{
if (method_exists($this, 'get_option'))
return call_user_func(array(
&$this,
'get_option'
), $key);
return $this->settings[$key];
}
public function _option_enabled($options_name, $enabled_string = 'yes')
{
is_array($options_name) || $options_name = explode(',', $options_name);
$result = true;
foreach ($options_name as $prop_name) {
$result = $result && isset($this->$prop_name) && ($enabled_string == $this->$prop_name);
}
return $result;
}
protected function get_supported_cards($default = false)
{
$result = array(
self::JCB => 'JCB',
self::AMEX => 'American Express',
self::VISA => 'Visa',
self::MASTER => 'Mastercard',
self::DINERS => 'Diner\'s Club',
self::MAESTRO => 'Maestro',
self::DISCOVER => 'Discover',
self::PAYPAL => 'PayPal'
);
return $default ? $result : array_intersect_key($result, array_flip($this->accepted_cards));
}
protected function get_supported_cards_icons()
{
if (! $this->_option_enabled('display_accepted_cards')) {
return false;
}
$result = array();
$class_names = array(
self::JCB => 'jcb',
self::AMEX => 'amex',
self::VISA => 'visa',
self::MASTER => 'mastercard',
self::DINERS => 'dinersclub',
self::MAESTRO => 'maestro',
self::DISCOVER => 'discover',
self::PAYPAL => 'paypal'
);
foreach ($this->get_supported_cards() as $issuer_id => $issuer_name) {
$tip = sprintf(__('%s %s is welcome', $this->id), $issuer_name, self::PAYPAL == $issuer_id ? __('payment', $this->id) : __('card', $this->id));
$result[$issuer_id] = sprintf('<div class="help_tip %s card-badge" title="%s" data-tip="%s"></div>', $class_names[$issuer_id] . '-badge', $tip, $tip);
}
return $result;
}
protected function get_supported_features()
{
return array(
self::DEFAULT_CREDIT_CARD_FORM
);
}
public function log($message, $notice_type = '')
{
return true;
}
public function settings_api_form_fields()
{
$form_fields = array();
foreach ($this->_fix_hidden_defaults($this->get_mynix_form_fields()) as $group => $group_fields)
$form_fields = $form_fields + $group_fields;
return $form_fields;
}
public function get_order_currency($order = false)
{
$order_currency = get_woocommerce_currency();
$order_id = - 1;
$order && $order_id = absint(get_query_var('order-pay'));
if ($order || 0 < $order_id) {
$order || $order = wc_get_order($order_id);
$order && method_exists($order, 'get_order_currency') && $order_currency = $order->get_order_currency();
}
return $order_currency;
}
protected function get_mynix_form_fields()
{
return array(
self::GROUP_OPTION_DEFAULT => array(
'reset' => array(
'type' => 'submit',
'title' => __('Reset settings to defaults', $this->id),
'desc_tip' => __('Clear your settings by reseting them to their initial factory defaults.', $this->id),
'description' => sprintf('<span style="color:#f00">%s</span>', __('This permanently removes your settings and set them to their initial values', $this->id)),
'default' => __('Reset settings', $this->id),
'css' => "width:auto;cursor:pointer",
'class' => 'button-primary mynix-button-primary',
'custom_attributes' => array(
'onclick' => "if(confirm('" . __('Are you really sure you want to clear your custom settings and replace them with the factory defaults?', $this->id) . "'))jQuery('form[name=\\'checkout\\']').submit();else return false;"
)
)
)
);
}
public function init_form_fields()
{
$this->form_fields = $this->settings_api_form_fields();
}
private function _fix_hidden_defaults($fields)
{
foreach ($fields as $group => $group_fields)
foreach ($group_fields as $key => $data)
if (isset($data['type']) && (false !== strpos($data['type'], 'hidden') || 'text' == $data['type']) && ! isset($data['options']) && ! is_string($data['default'])) {
$fields[$group][$key]['default'] = json_encode($data['default']);
}
return $fields;
}
public function generate_settings_html($form_fields = array(), $echo = true)
{
$has_visible_fields = function ($fields) {
$result = false;
foreach ($fields as $key => $props)
if (isset($props['type']) && false === strpos($props['type'], 'hidden')) {
$result = true;
break;
}
return $result;
};
$tab_slug = 'mynix-tab';
$form_fields = $this->_fix_hidden_defaults($this->get_mynix_form_fields());
reset($form_fields);
$groups = $this->get_settings_groups();
$groups_desc = $this->get_settings_groups_description();
$active_tab = isset($_POST['active-' . $tab_slug]) ? $_POST['active-' . $tab_slug] : ('#' . $tab_slug . key($form_fields));
echo '<table><tr><td><input type="hidden" name="active-' . $tab_slug . '" value="', $active_tab, '"/></td></tr></table>', PHP_EOL;
$tabs_content = array();
foreach ($form_fields as $group => $group_fields)
if (! empty($group_fields)) {
$jumper = '#' . $tab_slug . $group;
if (isset($groups[$group]) && ! empty($groups[$group]) && $has_visible_fields($group_fields)) {
$tabs[$group] = '<a href="#' . $tab_slug . $group . '" class="nav-tab nav-' . $tab_slug . ($jumper == $active_tab ? ' nav-tab-active' : '') . '" data-title="' . (isset($groups_desc[$group]) ? $groups_desc[$group] : '') . '">' . $groups[$group] . '</a>';
ob_start();
parent::generate_settings_html($group_fields);
$tabs_content[$group] = ob_get_clean();
}
}
$tabs[] = '<p id="nav-tab-description" class="description"></p>';
echo '<!-- mynix tabbed navigation starts here -->', PHP_EOL;
echo '<table class="form-table">';
echo '<tr class="', $tab_slug, '-wrapper"><td>', '<h2 class="nav-tab-wrapper">' . implode('', $tabs) . '</h2></td></tr>', PHP_EOL;
echo '<tr><td class="', $tab_slug, '-content">', PHP_EOL;
echo '<div class="mynix-settings-tabs">', PHP_EOL;
foreach ($tabs_content as $group => $content) {
$style = '#' . $tab_slug . $group == $active_tab ? '' : ' style="display:none"';
echo '<!-- ', $tab_slug, $group . ' content starts here -->', PHP_EOL;
echo '<table id="', $tab_slug, $group, '" class="', $tab_slug, '"', $style, '>', PHP_EOL;
echo $content, '</table>', PHP_EOL;
echo '<!-- ', $tab_slug, $group, ' content ends here -->', PHP_EOL;
}
echo '</div></td></tr>', PHP_EOL;
echo '</table>';
echo '<!-- mynix tabbed navigation ends here -->', PHP_EOL;
}
public function add_notice($message, $notice_type = 'success')
{
if (! is_admin() || (@constant('DOING_AJAX') && DOING_AJAX))
try {
if (function_exists('wc_add_notice')) {
return wc_add_notice($message, $notice_type); 
} else {
global $woocommerce;
$fct = 'add_' . ('error' != $notice_type ? 'message' : 'error');
if (method_exists($woocommerce, $fct)) {
return $woocommerce->$fct($message);
} else {
$type = 'error' == $notice_type ? 'errors' : 'messages';
$items = isset($_SESSION[$type]) ? $_SESSION[$type] : array();
$items[] = $message;
$_SESSION[$type] = $items;
return true;
}
}
} catch (\Exception $e) {}
return $this->alt_admin_notice($message, $notice_type);
}
public function supports($feature)
{
return in_array($feature, $this->get_supported_features());
}
public function validate_hidden_select_field($key)
{
$field = $this->_get_field_key($key);
$type = isset($_POST[$field]) && is_array($_POST[$field]) ? 'select' : 'text';
return call_user_func(array(
&$this,
'validate_' . $type . '_field'
), $key, null);
}
public function validate_hidden_field($key)
{
$field = $this->_get_field_key($key);
$type = isset($_POST[$field]) && is_array($_POST[$field]) ? 'multiselect' : 'text';
return call_user_func(array(
&$this,
'validate_' . $type . '_field'
), $key, null);
}
private function _generate_hidden_select_html($key, $data, $default_type)
{
$type = isset($data['options']) ? $default_type : 'text';
return preg_replace('/(<tr\\s+valign\\s*=\\s*["\']top[\'"])>/', '$1 style="display:none">', call_user_func(array(
&$this,
'generate_' . $type . '_html'
), $key, $data));
}
public function generate_hidden_select_html($key, $data)
{
return $this->_generate_hidden_select_html($key, $data, 'select');
}
public function generate_hidden_html($key, $data)
{
return $this->_generate_hidden_select_html($key, $data, 'multiselect');
}
private function legacy_get_custom_attribute_html($data)
{
if (method_exists($this, 'get_custom_attribute_html'))
return '';
$custom_attributes = array();
if (! empty($data['custom_attributes']) && is_array($data['custom_attributes'])) {
foreach ($data['custom_attributes'] as $attribute => $attribute_value) {
$custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
}
}
return implode(' ', $custom_attributes);
}
public function generate_inputbutton_html($key, $data)
{
$field = $this->generate_text_html($key, $data);
$button = array();
if (isset($data['button'])) {
$button[] = sprintf('type="%s"', isset($data['button']['type']) ? $data['button']['type'] : 'button');
$button[] = sprintf('class="%s"', isset($data['button']['class']) ? $data['button']['class'] : 'button');
foreach ($data['button'] as $attr => $value)
if (! in_array($attr, array(
'type',
'class'
)))
$button[] = sprintf('%s="%s"', $attr, $value);
$button = '<input ' . implode(' ', $button) . '/>';
}
return preg_replace(array(
'/(<input[^>]+>)/',
'/(<input\b.*?type=([\'"]))' . $data['type'] . '\2/'
), array(
'\1' . $button,
'\1text\2'
), $field);
}
public function generate_submit_html($key, $data)
{
return $this->generate_button_html($key, $data);
}
public function generate_caption_html($key, $data)
{
$html = parent::generate_text_html($key, $data);
return preg_replace('/<input(.+)value=([\'"])([^\2]*?)\2([^\/>]*).*/', '<code$1>$3</code>', $html);
}
public function generate_button_html($key, $data)
{
! isset($data['default']) && $data['default'] = 'value not specified';
return str_replace(array(
'type="text"',
'value=""'
), array(
'type="' . $data['type'] . '"',
'value="' . $data['default'] . '" ' . $this->legacy_get_custom_attribute_html($data)
), $this->generate_text_html($key, $data));
}
private function _fix_html_tooltip($html, $data)
{
if (empty($data['desc_tip']))
return $html;
$pattern = '/(?<=[\'"]titledesc[\'"])([\S\s]*?)<\s*\/\s*th/i';
if (preg_match($pattern, $html, $matches) && false === strpos($matches[0], 'help_tip')) {
$html = preg_replace($pattern, '$1' . $this->_get_tooltip_html($data) . '</th', $html);
}
return $html;
}
public function generate_text_html($key, $data)
{
return $this->_fix_html_tooltip(parent::generate_text_html($key, $data), $data);
}
public function generate_checkbox_html($key, $data)
{
return $this->_fix_html_tooltip(parent::generate_checkbox_html($key, $data), $data);
}
public function generate_multiselect_html($key, $data)
{
return $this->_fix_html_tooltip(parent::generate_multiselect_html($key, $data), $data);
}
public function generate_select_html($key, $data)
{
return $this->_fix_html_tooltip(parent::generate_select_html($key, $data), $data);
}
public function generate_textarea_html($key, $data)
{
return $this->_fix_html_tooltip(parent::generate_textarea_html($key, $data), $data);
}
public function validate_badge_field($key)
{
return $this->validate_text_field($key, null);
}
public function validate_caption_field($key)
{
return $this->validate_text_field($key, null);
}
protected function _get_pci_badge_text($default = false)
{
$default_text = __('100% PCI compliant');
if ($default)
return $default_text;
return isset($this->pci_text) ? $this->pci_text : $default_text;
}
protected function _get_pci_badge_url($default = false)
{
if ($default)
return plugins_url('assets/img/pci-badge.png', __DIR__);
return isset($this->pci_badge) ? $this->pci_badge : '';
}
protected function _get_pci_badge($default = false)
{
$pci_badge = '';
$pci_badge_url = $this->_get_pci_badge_url();
$pci_badge_desc = $this->_get_pci_badge_text();
if (! (empty($pci_badge_url) && empty($pci_badge_desc))) {
$pci_badge = '<table class="pci-badge"><tr><td class="pci-badge-icon">' . (empty($pci_badge_url) ? '' : ('<img src="' . $pci_badge_url . '">')) . '</td>' . (empty($pci_badge_desc) ? '' : ('<td class="pci-badge-text">' . $pci_badge_desc . '</td>')) . '</tr></table>';
}
return $pci_badge;
}
protected function _get_card_icons($style = '')
{
if ($supported_cards_icons = $this->get_supported_cards_icons()) {
return '<div' . ($style ? ' style="' . $style . '"' : '') . '>' . implode('', $supported_cards_icons) . '</div>';
}
return '';
}
protected function _get_badge()
{
$icons_position = isset($this->icons_position) ? $this->icons_position : 'icons_top';
switch ($icons_position) {
case 'icons_bottom':
$result = $this->_get_card_icons('display:inline-block;float: right;');
break;
default:
$result = $this->_get_badge_icon();
break;
}
return $result;
}
public function get_icon()
{
$icons_position = isset($this->icons_position) ? $this->icons_position : 'icons_top';
switch ($icons_position) {
case 'icons_top':
$result = $this->_get_card_icons('display:inline-block;float: right;');
break;
default:
$result = $this->_get_badge_icon();
break;
}
return $result;
}
public function on_admin_get_footer_text($text)
{
$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
if (! empty($request_uri) && preg_match('/.*\/(.*)\?.*tab=([^&]+)/', $_SERVER['REQUEST_URI'], $matches)) {
$page = $matches[1];
$tab = $matches[2];
if (! ('admin.php' == $page && 'checkout' == $tab))
return $text;
}
$plugin_data = get_plugin_data($this->get_plugin_mainfile());
return sprintf(__('If you like %s please leave us a %s rating. A huge thank you from %s in advance!', $this->id), get_anchor($plugin_data['PluginURI'], $plugin_data['Name']), get_anchor('https://wordpress.org/support/view/plugin-reviews/' . str_replace('-gateway', '', $this->id) . '?filter=5#postform', '★★★★★', 'wc-rating-link', false, '_blank', 'data-rated="Thanks :)"'), $plugin_data['Author']);
}
protected function get_known_locale()
{
return array(
'AU' => 'en_au',
'AT' => 'de_at',
'BE' => 'en_be',
'CA' => 'en_ca',
'DK' => 'da_dk',
'FR' => 'fr_fr',
'DE' => 'de_de',
'EE' => 'en_gb',
'GB' => 'en_gb',
'HK' => 'zh_hk',
'IT' => 'it_it',
'NL' => 'nl_nl',
'NO' => 'no_no',
'PL' => 'pl_pl',
'ES' => 'es_es',
'SE' => 'sv_se',
'CH' => 'en_ch',
'TR' => 'tr_tr',
'FI' => 'en_us',
'US' => 'en_us'
);
}
protected function _get_store_locale()
{
global $woocommerce;
$known_locale = $this->get_known_locale();
$locale = 'en_us';
$store_ctry = $woocommerce->countries->get_base_country();
isset($known_locale[$store_ctry]) && $locale = $known_locale[$store_ctry];
return $locale;
}
protected function _get_exchange_rate($from_currency, $to_currency, $amount = 0, $callback = null, $date = true)
{
if ($from_currency === $to_currency)
return $amount;
$date = true === $date ? time() : $date;
if ($exchage_rate = is_callable($callback) ? $callback($from_currency, $to_currency, $date) : false)
return $amount * $exchage_rate;
return false;
}
protected function get_order_payable_amount($order)
{
return apply_filters('woocommerce_order_amount_total', (double) $order->order_total, $order);
return $order->order_total;
$from_currency = $this->get_order_currency($order);
$to_currency = $this->conversion_currency; 
return $this->_get_exchange_rate($from_currency, $to_currency, $order->order_total, 'getExchRate', $order->order_date);
}
}
?>