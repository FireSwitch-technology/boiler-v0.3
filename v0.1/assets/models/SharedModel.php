<?php

abstract class SharedModel
 {

    public $conn;

    public function __construct( Database $database )
 {
        $this->conn = $database->connect();
    }


    public function connectToThirdPartyAPI( array $payload, string $url )
 {

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $payload ) );

        $response = curl_exec( $ch );
                                                                                                
        if ( $response === false ) {
            $error = curl_error( $ch );
            throw new Exception( $error );
        }

        curl_close( $ch );

        return $response;
    }

    public function respondWithInternalError( $errors ): void
 {

        // $this->outputData( false,  'Unable to process request, try again later',  $errors );
        ErrorHandler::handleException($errors);
    }

    #getUserdata::This method fetches All info related to a user

   // Your getUserdata function

   public function getUserdata(mixed $identifier)
   {
       $response = ['status' => false, 'data' => null, 'message' => null ];
   
       try {
           $sql = 'SELECT * FROM tblusers WHERE ';
           if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
               $sql .= 'mail = :mail';
           } else if (is_int($identifier)) {
               $sql .= 'usertoken = :usertoken';
           } else {
               $response['message'] = 'Invalid identifier type';
           }
   
           $stmt = $this->conn->prepare($sql);
           if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
               $stmt->bindParam(':mail', $identifier);
           } else {
               $stmt->bindParam(':usertoken', $identifier);
           }
           $stmt->execute();
   
           $user = $stmt->fetch(PDO::FETCH_ASSOC);
           if (empty($user)) {
               $response['message'] = 'User Does Not Exist';
           } else {
               $response['data']  = [
                   'fname' => $user['name'],
                   'mail' => $user['mail'],
                   'usertoken' => $user['usertoken'],
                   'phone' => $user['phone'],
               ];
               $response['status'] = true;
           }
   
       } catch (PDOException $e) {
           $response['message'] = 'Error retrieving user details: ' . $e->getMessage();
       } finally {
           $stmt = null;
       }
   
       return $response;
   }
   


    #authenticateUser:: This method authencticates User data

    public function authenticateUser( $usertoken )
 {
        try {

            $sql = 'SELECT usertoken FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':usertoken', $usertoken );
            $stmt->execute();

            if ( $stmt->rowCount() == 0 ) {

                $_SESSION[ 'err' ] = 'User does not exists';
                return false;
                exit;
            }

            return true;
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            return false;
        }
        finally {
            $stmt =  null;
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

    public function outputData( $success = null, $message = null, $data = null, $status_code = null)
 {
    http_response_code($status_code);

        $arr_output = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'status_code' => $status_code,  
        );
        echo json_encode( $arr_output );
    }
}
