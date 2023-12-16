<?php
require_once '../../assets/initializer.php';
$data = (array) json_decode(file_get_contents('php://input'), true);

$user = new Users($db);

$validKeys = ['identifier'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

if (!Utility::validateRequiredParams($data, $validKeys)) {
    return;
}

$getUserdata = $user->getUserData($data['identifier']);
if ($getUserdata['status']) {

    return $user->outputData(true, "fetched user data", $getUserdata['data'], 200);
}

$user->outputData(false, $getUserdata['message'], $getUserdata['data'], 500);

#Your method should be here
unset($user);
unset($db);
