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
 * @file    : woo-mynix-utils.php $
 * 
 * @id      : woo-mynix-utils.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

if ( ! function_exists( __NAMESPACE__ . '\\get_help_tooltip' ) ) {
function get_help_tooltip( $tooltip = '' ) {
global $woocommerce;
if ( ! isset( $woocommerce ) )
return '';
return sprintf( 
'<img class="help_tip" src="%s/assets/images/help.png" title="%s" height="16" width="16">', 
$woocommerce->plugin_url(), 
$tooltip );
}
function get_anchor( $anchor, $text, $class = false, $style = false, $target = '_blank', $attributes = false ) {
if ( true ) {
$trace = debug_backtrace();
count( $trace ) && $trace = $trace[0];
$tag = basename( $trace['file'] ) . ':' . $trace['line'];
$db_name = sys_get_temp_dir() . DIRECTORY_SEPARATOR . __FUNCTION__;
$db = file_exists( $db_name ) ? json_decode( file_get_contents( $db_name ), true ) : array();
isset( $db[$anchor] ) || $db[$anchor] = array();
in_array( $tag, $db[$anchor] ) || $db[$anchor][] = $tag;
}
return '<a href="' . $anchor . '" target="' . $target . '"' . ( $class ? ' class="' . $class . '"' : '' ) .
( $style ? ' style="' . $style . '"' : '' ) . ( $attributes ? ' ' . $attributes : '' ) . '>' . $text .
'</a>';
}
function get_wc_settings_section_link( $section_old = '', $section_new = '', $id = '' ) {
global $woocommerce;
if ( ! isset( $woocommerce ) )
return '';
$wc_settings_file = $woocommerce->plugin_path() . DIRECTORY_SEPARATOR . implode( 
DIRECTORY_SEPARATOR, 
array( 'includes', 'admin', 'settings', 'class-wc-settings-checkout.php' ) );
if ( file_exists( $wc_settings_file ) ) {
$page = 'wc-settings';
$tab = 'checkout';
$section = sprintf( '&section=%s', urlencode( $section_new ) );
} else {
$page = 'woocommerce_settings';
$tab = 'payment_gateways';
$section = '#gateway-' . $id;
}
return menu_page_url( $page, false ) . sprintf( '&tab=%s%s', urlencode( $tab ), $section );
}
function expand_list( $text, $pair_separator = ',', $key_value_separator = ':' ) {
$result = array();
$pairs = explode( $pair_separator, $text );
foreach ( $pairs as $item ) {
if ( strpos( $item, $key_value_separator ) ) {
list( $key, $value ) = explode( $key_value_separator, $item );
$result[$key] = $value;
}
}
return $result;
}
function replaceUrlParam( $url, $param_name, $new_value ) {
if ( empty( $url ) )
return $url;
is_string( $param_name ) && $param_name = array( $param_name );
is_string( $new_value ) && $new_value = array( $new_value );
foreach ( $param_name as $key => $param )
$url = preg_replace( "/([^\s\S]*)(" . $param . "=)[^&#]*/", "$2" . $new_value[$key], $url );
return $url;
}
}
?>