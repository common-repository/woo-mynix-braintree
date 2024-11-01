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
 * @file    : config.php $
 * 
 * @id      : config.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

defined(__NAMESPACE__."\\JS_NAMESPACE") || define ( __NAMESPACE__."\\JS_NAMESPACE" , "jsWooMynixBraintree" );
defined(__NAMESPACE__.'\\ROOT_PATH') || define(__NAMESPACE__.'\\ROOT_PATH',dirname(__FILE__).DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\INCLUDE_PATH') || define(__NAMESPACE__.'\\INCLUDE_PATH',ROOT_PATH.'include'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\ASSETS_PATH') || define(__NAMESPACE__.'\\ASSETS_PATH',ROOT_PATH.'assets'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\CSS_PATH') || define(__NAMESPACE__.'\\CSS_PATH',ASSETS_PATH.'css'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\IMG_PATH') || define(__NAMESPACE__.'\\IMG_PATH',ASSETS_PATH.'img'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\JS_PATH') || define(__NAMESPACE__.'\\JS_PATH',ASSETS_PATH.'js'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\LOCALE_PATH') || define(__NAMESPACE__.'\\LOCALE_PATH',ASSETS_PATH.'locale'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\CONFIG_PATH') || define(__NAMESPACE__.'\\CONFIG_PATH',ROOT_PATH.'config'.DIRECTORY_SEPARATOR);
defined(__NAMESPACE__.'\\APP_SLUG') || define(__NAMESPACE__.'\\APP_SLUG','woo-mynix-braintree');
defined(__NAMESPACE__.'\\CONFIG_PATH') && ($c=CONFIG_PATH.'config-custom.php') && file_exists($c) && (include_once $c) || define (__NAMESPACE__.'\\WOO_MYNIX_BRAINTREE_CONFIG_PATH_NOT_FOUND', 'CONFIG_PATH not defined. Your installation seems to be corupted.');
?>