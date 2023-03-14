<?php 
declare(strict_types=1);
require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');

header('Content-Type: application/json charset=utf-8');
header("Access-Control-Allow-Methods: PUT, GET, POST");
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,2));
$dotenv->load();
set_error_handler("ErrorHandler::handleError");
set_exception_handler("ErrorHandler::handleException");

$header = apache_request_headers();
// Get the Authorization header from the request
$authHeader = $header['Authorization'];
// Split the header into its parts
$parts = explode(' ', $authHeader);

// Extract the token portion
$token = $parts[1];

$db = new Database();
$auth = new Auth($db);


if ( ! $auth->authenticateAPIKey($token)) {
    exit;
}



