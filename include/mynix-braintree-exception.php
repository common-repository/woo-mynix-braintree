<?php
class MynixBraintreeException {
public static function parseException( $e, $url_reference = false ) {
switch ( get_class( $e ) ) {
case 'Braintree_Exception_Authentication' :
$message = 'API keys are incorrect';
break;
case 'Braintree_Exception_Authorization' :
$message = 'The API key is not authorized to perform this action';
break;
case 'Braintree_Exception_NotFound' :
$message = 'The record being operated cannot be found';
break;
case 'Braintree_Exception_UpgradeRequired' :
$message = 'The usage of this Braintree library is no longer supported';
break;
case 'Braintree_Exception_ServerError' :
$message = 'Braintree server error. Please try again in few minutes';
break;
case 'Braintree_Exception_DownForMaintenance' :
$message = 'Braintree server are down for maintenance. Please try again in few minutes';
break;
case 'Braintree_Exception_Unexpected' :
$message = 'Unexpected Braintree error. This should never happen.';
break;
case 'Braintree_Exception_ValidationsFailed' :
$message = 'Braintree gateway validations failed';
break;
case 'Braintree_Exception_Configuration' :
$message = 'Method not configured properly. Please report this incident.';
break;
case 'Braintree_Exception_ForgedQueryString' :
$message = 'Invalid (forged) hash in the query string.';
break;
case 'Braintree_Exception_InvalidSignature' :
$message = 'The webhook notification has an invalid signature';
break;
case 'Braintree_Exception_SSLCaFileNotFound' :
$message = 'The api_braintreegateway_com.ca.crt file is missing';
break;
case 'Braintree_Exception_SSLCertificate' :
$message = 'Cannot verify the SSL certificate (maybe cannot connect Braintre Gateway ?)';
break;
case 'Braintree\\Result\\Error' :
$message = $e->message;
break;
default :
$message = is_a( $e, 'Exception' ) ? $e->getMessage() : '';
empty( $message ) && $message = get_class( $e );
break;
}
$url_reference && $message = sprintf( 
'<a href="http://developers.braintreepayments.com/ios+php/reference/general/exceptions" target="_blank">%s</a>%s', 
get_class( $e ), 
empty( $message ) ? '' : ( ': ' . $message ) );
return $message;
}
}
?>