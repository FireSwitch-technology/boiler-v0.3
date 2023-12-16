<?php

class Users extends Model
 {


    public function registerUser( array $data )
 {

        $phone = ( int ) $data[ 'phone' ];

        // $checkIfMailExists = $this->checkIfMailExists( $data[ 'mail' ] );
        // if ( $checkIfMailExists ) {
        //     $this->outputData( false, 'Email already exists', null );
        //     return;
        // }
        $token = ( int ) Utility::token();

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
                $stmt->bindValue( $i,  ( $value ), $type );
                $i++;
            }
            $stmt->execute();


            if (Mailer::sendOTPToken( $data[ 'mail' ], $data[ 'name' ], '123' ) ) {
                unset( $mailer );
            }

            $output = $this->outputData( true, 'Account created', null, 201);
        } catch ( PDOException $e ) {

            $output  = $this->respondWithInternalError( 'Error: ' . $e->getMessage());
        }
        finally {
            $stmt  = null;
            $this->conn = null;

        }

        return $output;
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
        } catch ( PDOException $e ) {
            $this->respondWithInternalError($e->getMessage());
            return null;
        }
        finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    public function tryLogin( $data ) {

        try {
            $sql = 'SELECT * FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':mail', $data[ 'mail' ], PDO::PARAM_STR );
            $stmt->execute();
           

            $user = $stmt->fetch( PDO::FETCH_ASSOC );
            if (count($user) == 0) {
               return $this->outputData( false, 'No user found', null, 404);
            }


            if ( !password_verify( $data[ 'pword' ], $user[ 'pword' ] ) ) {
               return $this->outputData( false, "Incorrect password for $data[mail]", null, 401);
            }

            if ( $user ) {
                $userData = [
                    'name' => $user[ 'name' ],
                    'mail' => $user[ 'mail' ],
                    'usertoken' => $user[ 'usertoken' ],
                ];
            }
           return  $this->outputData( true, 'Login successful',  $userData ,200);

        } catch ( PDOException $e ) {
            $this->respondWithInternalError($e->getMessage());
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
            return false;
        }
        finally {
            $stmt = null;
        }
    }

    #Update Password:: This function updates a user Password

    public function updatePassword( array $data )
 {

        try {
            $sql = 'SELECT pword FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':usertoken', $data[ 'usertoken' ] , PDO::PARAM_INT);

            if ( $stmt->execute() ) {
                $dbPwd = $stmt->fetchColumn();
                $passHash = password_hash( $data[ 'npword' ], PASSWORD_DEFAULT, [ 12 ] );

                if ( password_verify( $data[ 'fpword' ], $dbPwd ) ) {

                  $updatePasswordInDB =   $this->updatePasswordInDB( $passHash, $data[ 'usertoken' ] );

                    if (!$updatePasswordInDB['status']) {
                        $this->outputData( false, $updatePasswordInDB[ 'message' ], null, 500);
                        return;
                    }
                    $this->outputData( true, 'Password Updated', null, 200);
                    return;

                } else {

                     return $this->outputData( false, 'Current password specified is not correct', null, 401 );
                   
                }
            }
        } catch ( PDOException $e ) {
            $this->respondWithInternalError($e->getMessage() );
        }
        finally {
            $stmt = null;
            $this->conn = null;

            #   Terminate the database connection
        }
    }

    # updatePasswordInDB::This function Updates users ppassword....

    private  function updatePasswordInDB( string $pword, int $usertoken )
 {
        $response = ['status' => false, 'message' => ''];

        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare( $sql );
            $stmt->bindParam( ':pword', $pword ,  PDO::PARAM_STR );
            $stmt->bindParam( ':usertoken', $usertoken ,  PDO::PARAM_INT );
            $stmt->execute();

            $response['status'] = true;
        } catch ( PDOException $e ) {
            $response['message'] = $e->getMessage();
        }
        finally {
            $stmt = null;
            $this->conn = null;
        }

        return $response;
    }

    public function forgetPword( array $data ) {

        $checkIfMailExists = $this->checkIfMailExists( $data[ 'mail' ] );
        if ( !$checkIfMailExists ) {
         return  $this->outputData( false, 'Email does not exists', null, 404 );

        }
        $token = Utility::token();

        $passHash = password_hash( $token, PASSWORD_DEFAULT );

        if ( !$this->resetPasswordInDB( $passHash, $data[ 'mail' ] ) ) {
           return $this->respondWithInternalError( $_SESSION[ 'err' ]);

        }

        $userData = $this->getUserData( $data[ 'mail' ] );

        if(!$userData['status']){
           return $this->outputData( false, $userData['message'], null, 500 );
        }

        try {
            if ( Mailer::sendPasswordToUser( $data[ 'mail' ], $userData['data'][ 'fname' ], $token ) ) {
                return $this->outputData( true, 'Password sent to mail', null , 200);

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
            $stmt->bindParam( ':pword', $pword ,  PDO::PARAM_STR );
            $stmt->bindParam( ':mail', $mail ,  PDO::PARAM_STR );
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

    

}
