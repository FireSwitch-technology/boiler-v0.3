<?php
require_once( '../assets/initializer.php' );
$data = ( array ) json_decode( file_get_contents( 'php://input' ), true );

$user = new Users($db);
$registerUser = $user->registerUser($data);
unset($user);
