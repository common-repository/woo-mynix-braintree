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
 * @file    : autoloader.php $
 * 
 * @id      : autoloader.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

global $classes_path_1258766967;
$classes_path_1258766967 = array (
'MynixBraintreeException' => INCLUDE_PATH . 'mynix-braintree-exception.php',
'MynixBraintreeHandler' => INCLUDE_PATH . 'mynix-braintree-handler.php',
'MynixQueryParam' => INCLUDE_PATH . 'mynix-braintree-handler.php',
'Xml2Array' => INCLUDE_PATH . 'Xml2Array.php'
);
spl_autoload_register ( function ($class_name) {
global $classes_path_1258766967;
$class_name = preg_replace ( "/" . __NAMESPACE__ . "\\\\/", "", $class_name );
isset ( $classes_path_1258766967 [$class_name] ) && include_once $classes_path_1258766967 [$class_name];});
?>