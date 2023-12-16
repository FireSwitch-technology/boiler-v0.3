<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, Content-Length, X-Requested-With');
header('Content-Type: application/json;charset=utf-8');

session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
set_error_handler('ErrorHandler::handleError');
set_exception_handler('ErrorHandler::handleException');

use Dotenv\Dotenv;

 $envMinePath = $_SERVER['DOCUMENT_ROOT'] . '/.env';

 $envMinePath = str_replace('\\', '/', $envMinePath);

$dotenv = Dotenv::createImmutable(dirname($envMinePath), '.env');
$dotenv->load();

$headers = apache_request_headers();
$authHeader = $headers['Authorization'] ?? "null";
$token = (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) ? $matches[1] : "null";

$db = new Database();
$auth = new Auth($db);
$authenticationResult = $auth->authenticateAPIKey($token);

if (!$authenticationResult['authenticated']) {
     $auth->outputData(false, $authenticationResult['message'], null);
     exit;

  }

