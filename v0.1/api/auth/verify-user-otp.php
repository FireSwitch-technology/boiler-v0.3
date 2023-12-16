<?php
require_once '../../assets/initializer.php';
$data = (array) json_decode(file_get_contents('php://input'), true);

$user = new Users($db);

$validKeys = ['mail', 'otp'];
#  Check for params  if matches required parametes
if (!Utility::validateRequiredParams($data, $validKeys)) {
    return;
}

$verifyUserOTP = $user->verifyUserOTP($data);
unset($user);
unset($db);
