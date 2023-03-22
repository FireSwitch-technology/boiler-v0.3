<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title></title>
    <style>
      /* inline CSS styles */
     <?php   require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/css/mailer.css');?>
    </style>
  </head>
  <body>
    <div class="container">
      <div class="logo-container">
        <img src="https://via.placeholder.com/150" alt="Company Logo">
      </div>
      <h1>Email Message</h1>
      <p>Hello,</p>
      <p>This is an example email message. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vel elit non elit ultricies faucibus eu ut urna. In hac habitasse platea dictumst. Sed faucibus auctor nulla. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia curae; Fusce gravida eros eu metus iaculis commodo.</p>
      <p><a href="#">Learn more</a></p>
      <p>Regards,</p>
      <p>Your Name</p>
    </div>
  </body>
</html>


<?php

declare(strict_types=1);

require_once($_SERVER['DOCUMENT_ROOT'] . '/boiler/vendor/autoload.php');

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
    exit(json_encode([ 'success' => false, 'message' => 'Authorization header is missing']));
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

// The API key is valid, continue with your logic here


