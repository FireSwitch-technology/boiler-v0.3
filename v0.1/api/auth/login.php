<?php
require_once( '../../assets/initializer.php' );
$data = ( array ) json_decode( file_get_contents( 'php://input' ), true );

$user = new Users( $db );

if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
    header( 'HTTP/1.1 405 Method Not Allowed' );
    header( 'Allow: POST' );
    exit();
}

#  Check for params  if matches required parametes
$validKeys = [ 'mail', 'pword' ];
if (!Utility::validateRequiredParams($data, $validKeys)) {
    return;
}
$registerUser = $user->tryLogin( $data );
unset( $user );
unset( $db );

