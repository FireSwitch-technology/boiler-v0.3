<?php

declare(strict_types=1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');
// require_once __DIR__ . 'vendor/autoload.php';


use Dotenv\Dotenv;

header('Content-Type: application/json;charset=utf-8');
header('Access-Control-Allow-Methods: PUT, GET, POST');

$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->load();

set_error_handler('ErrorHandler::handleError');
set_exception_handler('ErrorHandler::handleException');

$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? null;

if (!$authHeader) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'message' => 'Authorization header is missing']));
}

// Split the header into its parts
$parts = explode(' ', $authHeader);

// Extract the token portion
$token = $parts[1] ?? null;


$db = new Database();
$auth = new Auth($db);

if (!$auth->authenticateAPIKey($token)) {
    http_response_code(401);
}
exit;
// The API key is valid, continue with your logic here
