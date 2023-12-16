<?php

abstract class Model {

    public $conn;

  public function __construct(Database $database = null) {
        $this->conn = $database ? $database->connect() : null;
    }

    public function connectToThirdPartyAPI(array $payload, string $url) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            throw new Exception($error);
        }

        curl_close($ch);

        return $response;
    }

    public function respondWithInternalError($errors): void {

        // $this->outputData( false,  'Unable to process request, try again later',  $errors );
        ErrorHandler::handleException($errors);
    }

    #getUserdata::This method fetches All info related to a user

    // Your getUserdata function

    public function getUserData(mixed $identifier) {

        $response = ['status' => false, 'data' => null, 'message' => null, 'status_code' => 200];

        try {
            $sql = "SELECT * FROM tblusers WHERE";
            if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
                $sql .= " mail = :identifier";
            } else if (filter_var($identifier, FILTER_VALIDATE_INT)) {
                $sql .= " usertoken = :identifier";
            } else {
                $response['message'] = 'Invalid identifier type';
                return $response;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':identifier', $identifier);

            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($user)) {
                $response['message'] = "User Does Not Exist";
                $response['status_code'] = 404;
            } else {

                // $fetchAccountDetails = $this->fetchAccountDetails($user['renitoken']);
                $response['data'] = [
                    'fname' => $user['fname'],
                    'mail' => $user['mail'],
                    'lname' => $user['lname'],
                    'role' => $user['role'],
                    'is_verified' => ($user['is_verified'] == '1') ? true : false,
                    'renitoken' => $user['renitoken'],
                    // 'usertoken' => $user['usertoken'],
                    // 'accountDetails' => $fetchAccountDetails['data'] ?? [],
                    // 'accountBalance_th' => $fetchAccountDetails['data']['WithdrawableBalance_th'] ?? false,
                    // 'accountBalance' => $fetchAccountDetails['data']['WithdrawableBalance'] ?? false,
                    // 'accountNumber' => $fetchAccountDetails['data']['accountNumber'] ?? false,
                ];
                $response['status'] = true;
            }
        } catch (PDOException $e) {
            // Log the error for debugging purposes
            $response['message'] = 'An error occurred while processing your request.' . $e->getMessage();
        } finally {
            $stmt = null;
        }

        return $response;
    }

    #authenticateUser:: This method authencticates User data

    public function authenticateUser($usertoken) {
        try {

            $sql = 'SELECT usertoken FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $usertoken);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {

                $_SESSION['err'] = 'User does not exists';
                return false;
                exit;
            }

            return true;
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt = null;
        }
    }

#uploadImage:: This method uploads images to the server
    public function uploadImage(array $image): ?array {
        $imageInfo = array();

        # Get the image file information
        $imageName = $image['name'];
        $imageTmp = $image['tmp_name'];
        # Check if at least a profile image file is present
        if ((!isset($imageName) || empty($imageName))) {
            $_SESSION['err'] = "Please select an image to upload";
            return null;
        }

        # Valid file extensions
        $valid_extensions = array('jpg', 'jpeg', 'png', 'gif');

        # Test for profile image file extension
        if (isset($imageName) && !empty($imageName)) {
            $imageName_ext = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
            if (!in_array($imageName_ext, $valid_extensions)) {
                $_SESSION['err'] = "Only JPG, JPEG, PNG, and GIF files are allowed";
                return null;
            } else {
                # Save the property image file
                $mixImageNameWithTime = time() . '_' . $imageName;
                $newImageName = $_ENV['APP_NAME'] . '_' . $mixImageNameWithTime;
                $pathToImageFolder = ($_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $newImageName);
                if (!file_exists($imageTmp) || !is_readable($imageTmp)) {
                    $_SESSION['err'] = "Unable to upload the image. Please try again later";
                    return null;
                } else if (move_uploaded_file($imageTmp, $pathToImageFolder)) {
                    $imageInfo['image'] = $newImageName;

                } else {
                    $imageName = null;
                }
            }
        }
        http_response_code(200);
        return $imageInfo;
    }

#igniteReniConnection:: This method unboards a user to renitrust.
    public function igniteReniConnection($data) {
        $payload = [
            'mail' => $data['mail'],
            'firstname' => $data['fname'],
            'lastname' => $data['lname'],
            'username' => $data['fname'],
            'gender' => 'null',
            'phone' => $data['phone'],
        ];


        $url = $_ENV['RENI_SANDBOX'] . '/createUserProfile';

        try {
            $decodedResponse = $this->connectToReniTrust($payload, $url);
            if ($decodedResponse !== null) {

                if (isset($decodedResponse['success']) && $decodedResponse['success'] === true) {
                    $token = ($decodedResponse['data']['token']) ?? 0;
                    return $token;
                } else {
                    return 0;

                }
            }else{

                return 0;
            }

        } catch (Exception $e) {
            // Handle exceptions here
            $this->outputData(false, 'An error occurred while onboarding user: ' . $e->getMessage(), null, 500);
        }
    }

    #subscribeToNewsletter:: This method Automatically adds users to subcrivers list
    public function subscribeToNewsletter($data) {
        $payload = [
            'mail' => $data['mail'],
            'firstname' => $data['fname'],
            'lastname' => $data['lname'],
            'username' => $data['fname'],
            'gender' => 'null',
            'phone' => 'null',
        ];

        $url = $_ENV['RENI_MAIL'] . '/addContact';

        try {
            $decodedResponse = $this->connectToReniTrust($payload, $url);
            if ($decodedResponse !== null) {

                return $decodedResponse;

            } else {
                return false;
            }

        } catch (Exception $e) {
            // Handle exceptions here
            $this->outputData(false, 'An error occurred while onboarding user: ' . $e->getMessage(), null, 500);
        }
    }

#fetchAccountDetails:: This method fetches a  user account Details
    public function fetchAccountDetails($solar_reni_token) {

        $getUserAccountNumber = [
            'usertoken' => $solar_reni_token,
        ];

        $url = $_ENV['RENI_SANDBOX'] . '/getAccountBalance';

        $connectToReniTrust = $this->connectToReniTrust($getUserAccountNumber, $url);

        if ($connectToReniTrust !== null) {

            return $connectToReniTrust;

        } else {

            return false;
        }

    }







    #This method establishes the connection between the application and ReniTrust."
    public function connectToReniTrust(array $payload, $url) {
        $response = ['success' => false, 'message' => ''];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $headers = [
            'Authorization: Bearer ' . $_ENV['Enicom_Access_Bearer'],
            'Content-Type: application/json', // Added content type header
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        if ($result === false) {
            $response['message'] = "Unable to process request. Please contact support.".  curl_error($ch);
        } else {
            $response = json_decode($result, true); // Assuming the response is in JSON format
        }

        curl_close($ch);
        return $response;
    }

   

    public function outputData($success = null, $message = null, $data = null, $status_code = 200) {
        http_response_code($status_code);

        $arr_output = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'status_code' => $status_code,
        );
        echo json_encode($arr_output);
    }
}
