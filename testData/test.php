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
        $token = (int) $utility->token();
        $pword_hash = password_hash($data[ 'pword' ], PASSWORD_DEFAULT);

        #  Prepare the fields and values for the insert query
        $fields = [
            'name' => $data[ 'name' ],
            'mail' => $data[ 'mail' ],
            'phone' => $phone,
            'address' => $data[ 'address' ],
            // 'pword' => $pword_hash,
            // 'usertoken' => $token
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