<?php
require_once('../assets/initializer.php');
$data = (array) json_decode(file_get_contents('php://input'), true);

$product = new Product($db);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('HTTP/1.1 405 Method Not Allowed');
    header('Allow: POST');
    exit();
}

 $getPlatformProduct = $product->getPlatformProduct();
 


#Your method should be here
unset( $product );
unset( $db );
