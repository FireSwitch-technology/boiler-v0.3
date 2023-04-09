<?php

class Users
 {

    private $conn;

    public function __construct( Database $database )
 {

        $this->conn = $database->connect();
    }

    
    public function registerUser( array $data )
 {
        $utility = new Utility();

        #  Check for params  if matches required parametes
        $validKeys = [ 'name', 'mail', 'phone', 'address', 'pword' ];
        $invalidKeys = array_diff( array_keys( $data ), $validKeys );
        if ( !empty( $invalidKeys ) ) {
            foreach ( $invalidKeys as $key ) {
                $errors[] = "$key is not a valid input field";
            }

            if ( !empty( $errors ) ) {

                $this->respondUnprocessableEntity( $errors );
                return;
            }

        }

        #  Check for fields if empty
        foreach ( $validKeys as $key ) {
            if ( empty( $data[ $key ] ) ) {
                $errors[] = ( $key ) . ' is required';
            }
            if ( !empty( $errors ) ) {

                $this->respondUnprocessableEntity( $errors );
                return;
            }
        }

        $phone = ( int ) $data[ 'phone' ];

        $checkIfMailExists = $this->checkIfMailExists( $data[ 'mail' ] );
        if ( $checkIfMailExists ) {
            $output = $this->outputData( false, 'Email already exists', null );
            return;
        }
        $token = $utility->token();

        #  Prepare the fields and values for the insert query
        $fields = [
            'name' => $data[ 'name' ],
            'mail' => $data[ 'mail' ],
            'phone' => $phone,
            'address' => $data[ 'address' ],
            'usertoken' => $token
        ];

        # Build the SQL query
        $placeholders = implode( ', ', array_fill( 0, count( $fields ), '?' ) );
        $columns = implode( ', ', array_keys( $fields ) );
        $sql = "INSERT INTO tblusers ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt =  $this->conn->prepare($sql);
            $i = 1;
            foreach ( $fields as $value ) {
                $type = is_int( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue( $i,  $this->sanitizeInput( $value ), $type );
                $i++;
            }
            $stmt->execute();

            unset( $utility );
            #In order to free up memories, remmeber to  terminate   classes after instanciation..

            $mailer = new Mailer();

            if ( $mailer->sendOTPToken( $data[ 'mail' ], $data[ 'name' ], '123' ) ) {
                unset( $mailer );
                #In order to free up memories, remmeber to  terminate   classes after instanciation..
            }

            $this->conn = null;

            http_response_code( 201 );
            $output = $this->outputData( true, 'Account created', null );
        } catch ( PDOException $e ) {

            $output = $utility->outputData( false, 'Error: ' . $e->getMessage(), null );
        }

        return $output;
    }

    public function updateUser() {

    }

    /**
    * Save Profile Image
    *
    * @param [ type ] $propertyimage
    * @return array
    */

    public function saveProfileImage( $profileImage )
 {
        $imageInfo = array();

        # Get the image file information
        $profileImageName = $profileImage[ 'name' ];
        $profileImageTmp = $profileImage[ 'tmp_name' ];
        # Check if at least profile  image file is present
        if ( ( !isset( $propimage ) || empty( $propimage ) ) ) {
            http_response_code( 400 );
            $this->outputData( false, 'Image is required', null );
            return null;
        }

        # Valid file extensions
        $valid_extensions = array( 'jpg', 'jpeg', 'png', 'gif' );

        # Test for profile image file extension
        if ( isset( $propimage ) && !empty( $propimage ) ) {
            $propimage_ext = strtolower( pathinfo( $propimage, PATHINFO_EXTENSION ) );
            if ( !in_array( $propimage_ext, $valid_extensions ) ) {
                http_response_code( 422 );
                $this->outputData( false, 'File format not supported', null );
                return null;
            } else {
                # Save the property image  file
                $propnewFilename = time() . '_' . $propimage;
                $newProfileImageName = $_ENV[ 'APP_NAME' ] . '_' . $propnewFilename;
                $profileImagePath = ( $_SERVER[ 'DOCUMENT_ROOT' ] . '/uploads/' . $newProfileImageName );
                if ( !file_exists( $profileImageName ) || !is_readable( $profileImageTmp ) ) {
                    $propimage = null;
                } else if ( move_uploaded_file( $profileImageTmp, $profileImagePath ) ) {
                    $imageInfo[ 'profileImage' ] = $newProfileImageName;
                } else {
                    $propimage = null;
                }
            }
        }
        http_response_code( 200 );
        return $imageInfo;
    }

    /**
    * sanitizeInput Parameters
    *
    * @param [ type ] $input
    * @return string
    */
    private  function sanitizeInput( $input )
 {
        # Remove white space from beginning and end of string
        $input = trim( $input );
        # Remove slashes
        $input = stripslashes( $input );
        # Convert special characters to HTML entities
        $input = htmlspecialchars( $input, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

        return $input;
    }

    /**
    * resourceNotFound::Check for id if exists
    *
    * @param string $id
    * @return void
    */

    private function resourceNotFound( string $id ): void
 {
        http_response_code( 404 );
        echo json_encode( [ 'message' => "Resource with id $id not found" ] );
    }

    /**
    * Undocumented function
    *
    * @param int $id
    * @param array $data
    * @return integer
    */

    public function updateUserData( string $id, array $data ): int
 {
        $fields = [];

        if ( !empty( $data[ 'name' ] ) ) {
            $fields[ 'name' ] = [ $data[ 'name' ], PDO::PARAM_STR ];
        }

        if ( array_key_exists( 'phone', $data ) ) {
            $type = $data[ 'phone' ] === null ? PDO::PARAM_NULL : PDO::PARAM_INT;
            $fields[ 'phone' ] = [ $data[ 'phone' ], $type ];
        }

        if ( array_key_exists( 'address', $data ) ) {
            $fields[ 'is_completed' ] = [ $data[ 'is_completed' ], PDO::PARAM_BOOL ];
        }

        if ( empty( $fields ) ) {
            return 0;
        } else {
            $sql = 'UPDATE tblusers SET ';
            $params = [];
            foreach ( $fields as $name => $values ) {
                $sql .= "$name = ?, ";
                $params[] = $this->sanitizeInput( $values[ 0 ] );
            }

            $sql = rtrim( $sql, ', ' ) . ' WHERE id = ?';
            $params[] = $id;

            $stmt = $this->conn->prepare( $sql );
            $stmt->execute( $params );

            return $stmt->rowCount();
        }
    }

    /**
    * respondUnprocessableEntity alert of errors deteced
    *
    * @param array $errors
    * @return void
    */

    private function respondUnprocessableEntity( array $errors ): void
 {
        http_response_code( 422 );
        $this->outputData( false,  'Unable to process requests',  $errors );
    }

    private function checkIfMailExists( $mail ) {

        try {
            $sql = 'SELECT mail FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':mail', $mail, PDO::PARAM_STR );
            $stmt->execute();
            if ( $stmt->rowCount() > 0 ) {
                return true;
            } else {
                return false;
            }
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            $this->outputData( false, 'An error occurred while executing the query'.$_SESSION[ 'err' ], null );
        }
        finally {
            $this->conn = null;
            #   Terminate the database connection
        }
    }

    public function forgetPassword( $mail ) {

    }

    #Update Password:: This function updates a user Password

    public function updatePassword( $data ): void
 {
        $validKeys = [ 'usertoken', 'fpword', 'npword' ];
        $errors = [];

        #   Check if only valid input fields are provided
        $invalidKeys = array_diff( array_keys( $data ), $validKeys );
        if ( !empty( $invalidKeys ) ) {
            foreach ( $invalidKeys as $key ) {
                $errors[] = "$key is not a valid input field";
            }
            if ( !empty( $errors ) ) {
                $this->respondUnprocessableEntity( $errors );
                return;
            }
        }

        #   Check if required fields are empty
        foreach ( $validKeys as $key ) {
            if ( empty( $data[ $key ] ) ) {
                $errors[] = ( $key ) . ' is required';
            }
        }

        if ( !empty( $errors ) ) {
            $this->respondUnprocessableEntity( $errors );
            return;
        }
        try {
            $sql = 'SELECT pword FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':usertoken', $data[ 'usertoken' ] );

            if ( $stmt->execute() ) {
                $dbPwd = $stmt->fetchColumn();
                $passHash = password_hash( $data[ 'npword' ], PASSWORD_DEFAULT, [ 12 ] );

                if ( password_verify( $data[ 'fpword' ], $dbPwd ) ) {

                    if ( !$this->updatePasswordInDB( $passHash, $data[ 'usertoken' ] ) ) {
                        $this->outputData( false, $_SESSION[ 'err' ], null );
                        return;
                    }
                    echo 'done';

                } else {
                    $this->outputData( false, 'Current Password specified is not correct', null );
                }
            }
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            $this->outputData( false, 'An error occurred while executing the query'.$_SESSION[ 'err' ], null );
        }
        finally {
            $this->conn = null;
            #   Terminate the database connection
        }
    }

    # updatePasswordInDB::This function Updates users ppassword....

    public function updatePasswordInDB( string $pword, int $usertoken ): bool
 {
        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':pword', $pword );
            $stmt->bindParam( ':usertoken', $usertoken );
            $stmt->execute();
            return true;
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            return false;
        }
    }

    public function outputData( $success = null, $message = null, $data = null )
 {

        $arr_output = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
        );
        echo json_encode( $arr_output );
    }
}
