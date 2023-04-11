<?php

class Users extends AbstractClasses
 {

    private   $conn;

    public function __construct( Database $database )
 {

        $this->conn = $database->connect();
    }

    public function registerUser( array $data )
 {

        $phone = ( int ) $data[ 'phone' ];

        $checkIfMailExists = $this->checkIfMailExists( $data[ 'mail' ] );
        if ( $checkIfMailExists ) {
            $output = $this->outputData( false, 'Email already exists', null );
        }
        $token = ( int ) $this->token();
        $passHash = password_hash( $data[ 'pword' ], PASSWORD_DEFAULT );
        #  Prepare the fields and values for the insert query
        $fields = [
            'name' => $data[ 'name' ],
            'mail' => $data[ 'mail' ],
            'phone' => $phone,
            'address' => $data[ 'address' ],
            'pword'  => $passHash,
            'usertoken' => $token
        ];

        # Build the SQL query
        $placeholders = implode( ', ', array_fill( 0, count( $fields ), '?' ) );
        $columns = implode( ', ', array_keys( $fields ) );
        $sql = "INSERT INTO tblusers ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt =  $this->conn->prepare( $sql );
            $i = 1;
            foreach ( $fields as $value ) {
                $type = is_int( $value ) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue( $i,  $this->sanitizeInput( $value ), $type );
                $i++;
            }
            $stmt->execute();

            $mailer = new Mailer();

            if ( $mailer->sendOTPToken( $data[ 'mail' ], $data[ 'name' ], '123' ) ) {
                unset( $mailer );
            }

            http_response_code( 201 );
            $output = $this->outputData( true, 'Account created', null );
        } catch ( PDOException $e ) {

            $output  = $this->respondWithInternalError( 'Error: ' . $e->getMessage(), null );
        }
        finally {
            $this->conn = null;

        }

        return $output;
    }

    public function updateUser() {

    }

    #  SaveProfileImage::This methids save users profile image::

    public function saveProfileImage( array $profileImage ):array
 {
        $imageInfo = array();

        # Get the image file information
        $profileImageName = $profileImage[ 'name' ];
        $profileImageTmp = $profileImage[ 'tmp_name' ];
        # Check if at least profile  image file is present
        if ( ( !isset( $propimage ) || empty( $propimage ) ) ) {
            http_response_code( 400 );
            $this->outputData( false, 'Please select an image to upload', null );
            return null;
        }

        # Valid file extensions
        $valid_extensions = array( 'jpg', 'jpeg', 'png', 'gif' );

        # Test for profile image file extension
        if ( isset( $propimage ) && !empty( $propimage ) ) {
            $propimage_ext = strtolower( pathinfo( $propimage, PATHINFO_EXTENSION ) );
            if ( !in_array( $propimage_ext, $valid_extensions ) ) {
                http_response_code( 422 );
                $this->outputData( false, 'Only JPG, JPEG, PNG and GIF files are allowed.', null );
                return null;
            } else {
                # Save the property image  file
                $propnewFilename = time() . '_' . $propimage;
                $newProfileImageName = $_ENV[ 'APP_NAME' ] . '_' . $propnewFilename;
                $profileImagePath = ( $_SERVER[ 'DOCUMENT_ROOT' ] . '/uploads/' . $newProfileImageName );
                if ( !file_exists( $profileImageName ) || !is_readable( $profileImageTmp ) ) {
                    http_response_code( 422 );
                    $this->outputData( false, 'Unable to upload the profile image. Please try again later.', null );
                    return null;
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

    # updateUserData function updates user biodata

    public function updateUserData( int $id, array $data ): int {

        try {
            $updateQuery = 'UPDATE users SET ';
            $params = array();
            foreach ( $data as $key => $value ) {
                $updateQuery .= $key . ' = ?, ';
                $params[] = $value;
            }
            $updateQuery = rtrim( $updateQuery, ', ' ) . ' WHERE id = ?';
            $params[] = $id;

            $stmt = $this->conn->prepare( $updateQuery );
            $stmt->execute( $params );

            return $stmt->rowCount();
        } catch ( Exception $e ) {
            http_response_code( 500 );
            $this->outputData( false, 'Unable to update user data. Please try again later.', null);
            return 0;
        }
        finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    public function tryLogin( $data ):array|bool {

        try {
            $sql = 'SELECT * FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':mail', $data[ 'mail' ], PDO::PARAM_STR );
            $stmt->execute();
            if ( $stmt->rowCount() === 0 ) {
                $this->outputData( false, 'No user found', null );
                return false;
            }

            $user = $stmt->fetch( PDO::FETCH_ASSOC );

            if ( !password_verify( $data[ 'pword' ], $user[ 'pword' ] ) ) {
                $this->outputData( false, "Incorrect password for $data[mail]", null );
                return false;
            }

            if ( $user ) {
                $userData = [
                    'name' => $user[ 'name' ],
                    'mail' => $user[ 'mail' ],
                    'usertoken' => $user[ 'usertoken' ],
                ];
            }
            $this->outputData( true, 'Login successful',  $userData );
            return true;

        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            $this->respondWithInternalError( 'An error occurred while executing the query', $_SESSION[ 'err' ] );
        }
        finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    private function checkIfMailExists( string $mail ): bool {

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
            $this->respondWithInternalError( 'An error occurred while executing the query', $_SESSION[ 'err' ] );
        }
        finally {
            $stmt = null;
        }
    }

    #Update Password:: This function updates a user Password

    public function updatePassword( array $data ): void
 {

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
                    $this->outputData( true, 'Password Updated', null );
                    return;

                } else {

                    $this->outputData( false, 'Current Password specified is not correct', null );
                    return;
                }
            }
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            $this->respondWithInternalError( 'An error occurred while executing the query', $_SESSION[ 'err' ] );
        }
        finally {
            $stmt = null;
            $this->conn = null;

            #   Terminate the database connection
        }
    }

    # updatePasswordInDB::This function Updates users ppassword....

    private  function updatePasswordInDB( string $pword, int $usertoken ): bool
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
        finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    public function forgetPword( array $data ):bool {

        $checkIfMailExists = $this->checkIfMailExists( $data[ 'mail' ] );
        if ( !$checkIfMailExists ) {
            $this->outputData( false, 'Email does not exists', null );
            return false;

        }
        $token = $this->token();
        $passHash = password_hash( $token, PASSWORD_DEFAULT );

        if ( !$this->resetPasswordInDB( $passHash, $data[ 'mail' ] ) ) {
            $this->respondWithInternalError( $_SESSION[ 'err' ], null );

            return false;
        }

        $mailer = new Mailer;
        $userData = $this->getUserData( $data[ 'mail' ] );

        try {
            if ( $mailer->sendPasswordToUser( $data[ 'mail' ], $userData[ 'name' ], $token ) ) {
                $this->outputData( true, 'Password sent to mail', null );
                return true;

            }
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
        }
        finally {
            unset( $mailer );
        }

    }

    private function resetPasswordInDB( string $pword, string $mail ): bool
 {
        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE mail = :mail';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':pword', $pword );
            $stmt->bindParam( ':mail', $mail );
            $stmt->execute();
            return true;
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
            return false;
        }
        finally {
            $stmt = null;
        }
    }

    private function getUserData( string $mail ): ?array {
        $userData = null;
        try {
            $sql = 'SELECT usertoken, name, mail FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':mail', $mail );
            $stmt->execute();
            $user = $stmt->fetch( PDO::FETCH_ASSOC );
            if ( $user ) {
                $userData = [
                    'name' => $user[ 'name' ],
                    'mail' => $user[ 'mail' ],
                    'usertoken' => $user[ 'usertoken' ],
                ];
            }
        } catch ( PDOException $e ) {
            $_SESSION[ 'err' ] = $e->getMessage();
        }
        finally {
            $stmt = null;
            $this->conn = null;
        }
        return $userData;
    }

}
