<?php

class Auth extends SharedModel
{

  public function authenticateAPIKey($api_key): array {
    $response = ['authenticated' => false, 'message' => ''];

    if (empty($api_key)) {
        $response['message'] = 'Missing API key';
        return $response;
    }

    if ($api_key !== $_ENV['APP_TOKEN']) {
        $response['message'] = 'API key is invalid';
        return $response;
    }

    $response['authenticated'] = true;
    return $response;
}
}
