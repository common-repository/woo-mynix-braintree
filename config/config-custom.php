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
 * @file    : config-custom.php $
 * 
 * @id      : config-custom.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

require_once INCLUDE_PATH . 'autoloader.php';
require_once INCLUDE_PATH . 'woo-mynix-utils.php';
define( __NAMESPACE__.'\\DELAY_BRAINTREE_SDK', 3000 );
if ( function_exists( 'get_plugins' ) ) {
foreach ( array_keys( \get_plugins() ) as $plugin_file )
if ( 'woocommerce.php' == basename( $plugin_file ) ) {
include_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . $plugin_file;
break;
}
}
$_files_ = glob( __DIR__ . DIRECTORY_SEPARATOR . 'config-*.php' );
asort( $_files_ );
foreach ( $_files_ as $_config_file_ )
if ( $_files_ != __FILE__ )
include_once $_config_file_;
?>