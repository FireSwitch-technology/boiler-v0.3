<?php
    require_once('../assets/initializer.php');
    $data = (array) json_decode(file_get_contents("php://input"), true);
    $Utility = new Utility;
    // if ($Utility->validatePayload($data)) {
    //     return false;
    // }

    $new = new  Mailer;



    $var = $new->sendOTPToken();
