<?php
require_once('../assets/initializer.php');
$data = (array) json_decode(file_get_contents('php://input'), true);

$user = new Users($db);


$validKeys = ['mail'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}


if (!Utility::validateFileUpload(['image'], 'image')) {
    return;
}

#Your method should be here

$uploadImageToServer = $user->uploadImage($_FILES['image']);
if ($uploadImageToServer !== null) {
    $product->outputData(true, 'Fetched image', $uploadImageToServer, 200);
} else {
    $product->outputData(false, $_SESSION['err'], null, 500);
}

#Your method should be here
unset( $user );
unset( $db );
