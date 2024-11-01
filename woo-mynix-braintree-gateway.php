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
 * @file    : woo-mynix-braintree-gateway.php $
 * 
 * @id      : woo-mynix-braintree-gateway.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

/**
 * Plugin Name: Woo Braintree Payment
 * Plugin URI: http://mynixworld.info/woo-braintree
 * Description: Extends WooCommerce by adding the Braintree Payment Gateway
 * Version: 0.2-1.1
 * Author: Eugen Mihailescu, MyNixWorld
 * Author URI: http://mynixworld.info
 * Developer: Eugen Mihailescu
 * Developer URI: http://mynixworld.info
 *
 * Text Domain: woo-mynix-braintree-gateway
 * Domain Path: /assets/locale/
 *
 * Copyright: © 2015 MyNixWorld.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WooCommerce
 * @category Plugin
 * @author Eugen Mihailescu
 */
! @constant( 'ABSPATH' ) && exit();
! defined( __NAMESPACE__.'\\APP_ADDONS_SHOP_URI' ) && define( __NAMESPACE__.'\\APP_ADDONS_SHOP_URI', 'http://mynixworld.info/shop/' );
require_once __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
include_once (\ABSPATH . 'wp-admin/includes/plugin.php' );
add_action( 'admin_notices', __NAMESPACE__ . '\\woo_mynix_braintree_already_exists', 0, 1 );
function woo_mynix_braintree_active() {
$current_plugin_dir = basename( __DIR__ );
$current_plugin_file = basename( __FILE__ );
foreach ( get_plugins() as $slug => $plugin_info ) {
if ( dirname( $slug ) != $current_plugin_dir && basename( $slug ) == $current_plugin_file &&
is_plugin_active( $slug ) )
return $plugin_info;
}
return false;
}
function woo_mynix_braintree_already_exists() {
if ( $plugin_info = woo_mynix_braintree_active() ) {
$plugin_data = get_plugin_data( __FILE__ );
$class = "error";
$message = sprintf( 
__( "%s : an instance of %s already running.", $plugin_data['TextDomain'] ), 
$plugin_data['Name'], 
$plugin_info['Name'] );
echo "<div class=\"$class\"> <p>$message</p></div>";
deactivate_plugins( plugin_basename( __FILE__ ) );
}
}
if ( false === woo_mynix_braintree_active() ) {
define( __NAMESPACE__.'\\MYNIX_BRAINTREE_INTERFACE_CLASS', 'Legacy_Woo_Mynix_Braintree_Gateway' );
define( __NAMESPACE__.'\\MYNIX_BRAINTREE_INTERFACE', 'include/woo-mynix-braintree-legacy' );
add_action( 'plugins_loaded', __NAMESPACE__ . '\\woo_mynix_braintree_init', 1 );
add_filter( 
'plugin_action_links_' . plugin_basename( __FILE__ ), 
__NAMESPACE__ . '\\woo_mynix_braintree_settings_link' );
add_filter( 'plugin_row_meta', __NAMESPACE__ . '\\woo_mynix_braintree_plugin_row_meta', 10, 4 );
function woo_mynix_braintree_init() {
$plugin_data = get_plugin_data( __FILE__ );
$loaded = load_plugin_textdomain( 
$plugin_data['TextDomain'], 
false, 
plugin_basename( dirname( __FILE__ ) ) . '/assets/locale' );
$compatible_environment = woo_mynix_braintree_checks( false );
if ( true !== $compatible_environment ) {
deactivate_plugins( plugin_basename( __FILE__ ) );
return;
}
include_once MYNIX_BRAINTREE_INTERFACE . '.php';
add_filter( 'woocommerce_payment_gateways', __NAMESPACE__ . '\\woo_mynix_braintree_add_gateway' );
do_action( 'mynix_init_updater', __FILE__ );
}
function woo_mynix_braintree_checks( $skip_notices = false ) {
$plugin_data = get_plugin_data( __FILE__ );
$class = "error";
$messages = array();
$php_min_version = '5.3';
$php_version = phpversion();
$wc_min_version = '1.6.6';
$wc_version = @constant( 'WC_VERSION' ) ? WC_VERSION : ( @constant( 'WOOCOMMERCE_VERSION' ) ? WOOCOMMERCE_VERSION : '0.0' );
if ( version_compare( $php_version, $php_min_version, '<' ) ) {
$messages[] = sprintf( 
__( '%s requires PHP %s+. Your PHP version is %s.', $plugin_data['TextDomain'] ), 
$plugin_data['Name'], 
$php_min_version, 
$php_version );
}
if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
$messages[] = __( 
sprintf( 
'WooCommerce is not installed (or activated). %s cannot work without it.', 
$plugin_data['Name'] ), 
$plugin_data['TextDomain'] );
} elseif ( version_compare( $wc_version, $wc_min_version, '<' ) ) {
$messages[] = sprintf( 
__( '%s requires WooCommerce %s+. Your WooCommerce version is %s.', $plugin_data['TextDomain'] ), 
$plugin_data['Name'], 
$wc_min_version, 
$wc_version );
}
if ( ! $skip_notices ) {
foreach ( $messages as $message ) {
echo "<div class=\"$class\"> <p>$message</p></div>";
}
}
return empty( $messages ) ? true : $messages;
}
function woo_mynix_braintree_add_gateway( $methods ) {
$methods[] = MYNIX_BRAINTREE_INTERFACE_CLASS;
return $methods;
}
function woo_mynix_braintree_settings_link( $links ) {
if ( ! class_exists( 'WC_Payment_Gateway' ) )
return $links;
$plugin_data = get_plugin_data( __FILE__ );
$section = strtolower( str_replace( '\\', '_', MYNIX_BRAINTREE_INTERFACE_CLASS ) );
$parent_class = get_parent_class( MYNIX_BRAINTREE_INTERFACE_CLASS );
$id = str_replace( '_', '-', strtolower( $parent_class ) );
if ( $p = strpos( $id, '\\' ) )
$id = substr( $id, $p + 1 );
$url = get_wc_settings_section_link( $section, $section, $id );
$plugin_links = array();
empty( $url ) ||
$plugin_links[] = get_anchor( $url, __( 'Settings', $plugin_data['TextDomain'] ), false, false, '_self' );
return array_merge( $plugin_links, $links );
}
function woo_mynix_braintree_plugin_row_meta( $plugin_meta, $plugin_file = null, $plugin_data = null, $status = null ) {
if ( basename( __DIR__ ) . DIRECTORY_SEPARATOR . basename( __FILE__ ) == $plugin_file &&
class_exists( 'WC_Payment_Gateway' ) ) {
if ( $pro_class_exists = class_exists( __NAMESPACE__ . '\\Woo_Mynix_Braintree_Gateway_Pro' ) ) {
$plugin_meta_last = end( $plugin_meta );
$plugin_meta[count( $plugin_meta ) - 1] = sprintf( 
'<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>', 
esc_url( 
network_admin_url( 
'plugin-install.php?tab=plugin-information&plugin=' . $plugin_data['TextDomain'] . '-pro' .
'&TB_iframe=true&width=600&height=550' ) ), 
esc_attr( sprintf( __( 'More information about %s' ), $plugin_data['Name'] ) ), 
esc_attr( $plugin_data['Name'] ), 
__( 'View details' ) );
$plugin_meta[] = $plugin_meta_last;
}
$plugin_meta['faq'] = get_anchor( 
APP_ADDONS_SHOP_URI . 'faq-woo-mynix-braintree', 
__( 'FAQ', $plugin_data['TextDomain'] ) );
$plugin_meta['docs'] = get_anchor( 
APP_ADDONS_SHOP_URI . 'tutorials', 
__( 'Docs', $plugin_data['TextDomain'] ) );
if ( ! $pro_class_exists )
$plugin_meta['pro'] = get_anchor( 
APP_ADDONS_SHOP_URI . 'shop/woo-mynix-braintree-pro', 
__( 'Upgrade to PRO', $plugin_data['TextDomain'] ), 
false, 
'color:#FF5950;font-weight:bold;' );
}
return $plugin_meta;
}
}
?>