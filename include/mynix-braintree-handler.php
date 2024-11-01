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
 * @file    : mynix-braintree-handler.php $
 * 
 * @id      : mynix-braintree-handler.php | Thu Dec 8 12:16:22 2016 +0100 | eugenmihailescu <eugenmihailescux@gmail.com> $
*/

namespace WooMynixBraintree;

! @constant( 'ABSPATH' ) && exit();
require_once dirname( __DIR__ ) . '/assets/vendor/braintree/lib/Braintree.php';
class MynixQueryParam {
public $name;
public $value;
function __construct( $name, $value ) {
$this->name = $name;
$this->value = $value;
}
public function toparam() {
return $this->value;
}
}
class MynixBraintreeHandler {
private $_output_filename;
private $_payment_gateway;
private $_initialized;
private function _parseException() {
return call_user_func_array( 'MynixBraintreeException::parseException', func_get_args() );
}
private function _catch_output() {
ob_start();
}
private function _log_output() {
$output = ob_get_clean();
! empty( $output ) &&
@file_put_contents( $this->_output_filename, date( 'Y-m-d H:i:s' ) . PHP_EOL . $output . PHP_EOL, 8 );
}
function __construct( $payment_gateway ) {
$this->_output_filename = __FILE__ . '.log';
$this->_payment_gateway = $payment_gateway;
$this->_initialized = $this->init();
}
function __destruct() {
if ( is_file( $this->_output_filename ) && filesize( $this->_output_filename ) &&
is_object( $this->_payment_gateway ) )
$this->_payment_gateway->add_notice( 
sprintf( 
__( 'Unexpected PHP output. Check the %s file', $this->_payment_gateway->id ), 
str_replace( ABSPATH, '', $this->_output_filename ) ), 
'error' );
}
private function _transaction( $method ) {
$this->_catch_output();
$args = func_get_args();
array_shift( $args );
$errors = array();
try {
set_time_limit( 0 );
$result = call_user_func_array( "\\Braintree_Transaction::$method", $args );
if ( ! $result->success ) {
if ( $result->errors->deepSize() > 0 ) {
foreach ( $result->errors->deepAll() as $error ) {
$errors[] = $error->message;
}
} else {
$error_code = - 1;
$error_message = __( 'Failed', $this->_payment_gateway->id );
switch ( $result->transaction->status ) {
case 'processor_declined' :
$error_code = $result->transaction->processorResponseCode;
$error_message = $result->transaction->processorResponseText;
! empty( $result->transaction->additionalProcessorResponse ) &&
$error_message .= PHP_EOL . $result->transaction->additionalProcessorResponse;
break;
case 'settlement_declined' :
$error_code = $result->transaction->processorSettlementResponseCode;
$error_message = $result->transaction->processorSettlementResponseText;
break;
case 'gateway_rejected' :
$error_message = $result->transaction->gatewayRejectionReason;
break;
case 'failed' :
}
{
$obj = $this->_payment_gateway;
$errors[] = sprintf( 
__( 
'The %s payment transaction failed with status %s:<br>%s (%s).<br>Please contact us for further information.', 
$this->_payment_gateway->id ), 
$obj::VENDOR, 
'<strong>' . $result->transaction->status . '</strong>', 
$error_message, 
$error_code );
}
}
} else {
$this->_log_output();
return array( 'txid' => $result->transaction->id );
}
} catch ( \Exception $e ) {
$err_msg = $this->_parseException( $e );
$init_error = is_array( $this->_initialized ) ? PHP_EOL . $this->_initialized['error'] : '';
$errors[] = __( 
'We are currently experiencing problems trying to process your request.', 
$this->_payment_gateway->id ) . ' ' . $err_msg . $init_error;
}
if ( count( $errors ) > 1 ) {
$this->_payment_gateway->log( 
$this->_payment_gateway->get_gateway_vendor() . ' transaction failed due to following reasons:' . PHP_EOL .
' - ' . implode( PHP_EOL . ' - ', $errors ), 
'error' );
}
$this->_log_output();
return array( 'errors' => $errors );
}
public function get_client_token( $url_reference = false, $merchant_account_id = '', $skip_nonce = false ) {
$result = false;
$this->_catch_output();
$skip_nonce || $nonce = $_POST['mynix_nonce'];
try {
if ( ! ( $skip_nonce || wp_verify_nonce( $nonce, 'mynix-client-token-nonce' ) ) ) {
$result = array( 'error' => __( 'Access denied!', $this->_payment_gateway->id ) );
} elseif ( ! ( $skip_nonce || $this->_payment_gateway->is_available() ) ) {
if ( $this->_payment_gateway->_option_enabled( 'debug_mode' ) ) {
$anchor = '?page=wc-status&tab=logs';
$text = ' ' . __( 'Check the log.' );
} else {
$anchor = APP_ADDONS_SHOP_URI . '/faq-woo-mynix-braintree/#q7';
$text = __( 'Check the FAQ', $this->_payment_gateway->id );
}
$result = array( 
'error' => sprintf( 
__( 'The payment gateway is not available.%s', $this->_payment_gateway->id ), 
get_anchor( $anchor, $text, false, false, '_self' ) ) );
} elseif ( true !== ( $result = $this->_initialized ) ) {
$pg = $this->_payment_gateway;
$result = array( 
'error' => sprintf( 
__( 'The %s environment could not be initialized.', $this->_payment_gateway->id ), 
$pg::VENDOR ) );
} else {
$args = array();
$merchant_account_id && $args['merchantAccountId'] = $merchant_account_id;
$result = array( 'token' => \Braintree_ClientToken::generate( $args ) );
}
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e, $url_reference ) );
}
$this->_log_output();
return $result;
}
public function init( $return_errors = false ) {
$result = false;
$this->_catch_output();
try {
\Braintree_Configuration::environment( $this->_payment_gateway->get_environment() );
\Braintree_Configuration::merchantId( $this->_payment_gateway->get_merchant_id() );
\Braintree_Configuration::publicKey( $this->_payment_gateway->get_public_key() );
\Braintree_Configuration::privateKey( $this->_payment_gateway->get_private_key() );
$result = true;
} catch ( \Exception $e ) {
$err_msg = $this->_parseException( $e );
if ( ! $return_errors )
$this->_payment_gateway->add_notice( $err_msg, 'error' );
else {
$result = array( 'error' => $err_msg );
}
}
$this->_log_output();
return $result;
}
public function sale( $order ) {
if ( true === ( $payload = $this->_initialized ) ) {
$ignore_order_items = array();
$paymentMethodToken = false;
$subscriptions = false;
if ( has_filter( 'mynix_subscription_payload' ) ) {
$subscription_payloads = false;
$subscription_payloads = apply_filters( 'mynix_subscription_payload', $order );
if ( ! empty( $subscription_payloads ) && has_filter( 'mynix_subscription_create' ) ) {
$subscriptions = apply_filters( 'mynix_subscription_create', $subscription_payloads );
if ( isset( $subscriptions['errors'] ) ) {
return array( 'errors' => implode( '<br>', $subscriptions['errors'] ) );
}
foreach ( $subscriptions as $order_item_id => $subscription )
if ( ! is_array( $subscription ) ) {
$paymentMethodToken = $subscription->subscription->paymentMethodToken;
break;
}
$ignore_order_items = array_keys( $subscription_payloads['plans'] );
}
}
$payload = $this->_payment_gateway->get_payload( $order, $ignore_order_items );
if ( ! isset( $payload['error'] ) ) {
if ( $paymentMethodToken ) {
$payload['paymentMethodToken'] = $paymentMethodToken;
unset( $payload['paymentMethodNonce'] );
}
if ( floatval( $payload['amount'] ) > 0 ) {
return $this->_transaction( 'sale', $payload );
} elseif ( $subscriptions ) {
$result = array();
foreach ( $subscriptions as $order_item_it => $subscription )
$result[] = $subscription->subscription->id;
return array( 'txid' => implode( ';', $result ) );
}
$payload['error'] = __( 'Unexpected null order amount' );
}
}
return array( 'errors' => $payload['error'] );
}
public function refund( $txid, $amount ) {
if ( true !== ( $result = $this->_initialized ) ) {
$this->_payment_gateway->log( 
__( 'Refund Failed:', $this->_payment_gateway->id ) . ' ' . implode( PHP_EOL, $result['error'] ) );
return array( - 1, $result['error'] );
}
$result = $this->_transaction( 'refund', $txid, $amount );
return isset( $result['txid'] ) ? $result['txid'] : $result['errors'];
}
public function cancel_refund( $txid ) {
if ( true === ( $payload = $this->_initialized ) ) {
return $this->_transaction( 'void', $txid );
}
return array( 'errors' => $payload['error'] );
}
public function test_connection( $url_reference = false ) {
return $this->get_client_token( $url_reference );
}
public function get_recurring_billing_plans() {
$this->_catch_output();
try {
$result = \Braintree_Plan::all();
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e ) );
}
$this->_log_output();
return $result;
}
public function create_payment_method( $attributes ) {
$this->_catch_output();
$result = false;
if ( isset( $attributes['orderId'] ) ) {
$order = new \WC_Order( $attributes['orderId'] );
if ( $user_id = $order->get_user_id() ) {
$meta_key = '_braintree_customer_id';
$user_meta = get_user_meta( $user_id, $meta_key );
$braintree_customer_id = end( $user_meta );
if ( ! empty( $braintree_customer_id ) ) {
$customer_id = end( $braintree_customer_id );
if ( ! empty( $customer_id ) ) {
try {
$result = \Braintree_Customer::find( $customer_id );
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e ) );
}
}
}
}
}
if ( ! $result ) {
$query = array();
foreach ( $attributes['customer'] as $key => $value ) {
$node = new \Braintree_TextNode( $key );
$query[] = $node->is( $value );
}
try {
$result = \Braintree_Customer::search( $query );
if ( 0 == count( $result ) )
$result = false;
elseif ( count( $result ) > 1 )
$result = array( 
'error' => __( 'Multiple customers with the same arguments', $this->_payment_gateway->id ) );
else {
$result = end( $result );
$customer_id = current( $result );
$customer = \Braintree_Customer::find( $customer_id );
$result = \Braintree_PaymentMethod::create( 
array( 'customerId' => $customer_id, 'paymentMethodNonce' => $attributes['paymentMethodNonce'] ) );
if ( ! $result->success )
$result = $this->_parseException( $result );
else
$result = $result->paymentMethod;
}
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e ) );
}
}
if ( ! $result ) {
try {
$customer_attribs = array_merge( 
$attributes['customer'], 
array( 
'creditCard' => array( 'billingAddress' => $attributes['billing'] ), 
'paymentMethodNonce' => $attributes['paymentMethodNonce'] ) );
$result = \Braintree_Customer::create( $customer_attribs );
if ( false === $result->success ) {
$this->_log_output();
return $this->_parseException( $result );
}
do_action( 'mynix_new_vault_customer', $attributes['orderId'], $result->customer->id );
$result = $result->paymentMethods[0];
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e ) );
}
}
$this->_log_output();
return $result;
}
public function create_subscription( $planId, $attributes ) {
$payment_method = $this->create_payment_method( $attributes );
$this->_catch_output();
if ( is_array( $payment_method ) && isset( $payment_method['error'] ) )
return $payment_method;
try {
$args = array( 'paymentMethodToken' => $payment_method->token, 'planId' => $planId );
isset( $attributes['merchantAccountId'] ) && $args['merchantAccountId'] = $attributes['merchantAccountId'];
$result = \Braintree_Subscription::create( $args );
if ( ! $result->success ) {
$result = array( 'error' => $this->_parseException( $result ) );
}
} catch ( \Exception $e ) {
$result = array( 'error' => $this->_parseException( $e ) );
}
$this->_log_output();
return $result;
}
}
?>