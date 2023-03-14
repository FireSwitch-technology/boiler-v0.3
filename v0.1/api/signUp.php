<?php
    require_once('../assets/initializer.php');
    $data = file_get_contents('php://input');
    $data = json_decode($data, true);
    $Utility = new Utility;
    if ($Utility->validatePayload($data)) {
       
        exit;
    }
