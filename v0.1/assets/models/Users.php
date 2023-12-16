<?php

class Users extends Model {

    public function registerUser(array $data) {

        $checkIfMailExists = $this->checkIfMailExists($data['mail']);
        if ($checkIfMailExists) {
            $this->outputData(false, 'Email already exists', null, 409);
            return;
        }
        $token = (int) Utility::token();

        $passHash = password_hash($data['pword'], PASSWORD_DEFAULT);

        $reniPayload = [
            'mail' => $data['mail'],
            'fname' => $data['fname'],
            'lname' => $data['lname'],
             'phone' => $data['phone'],

        ];

        #Unboard user to renitrust.
        // $renitoken = $this->igniteReniConnection($reniPayload);

        #  Prepare the fields and values for the insert query
        $fields = [
            'fname' => $data['fname'],
            'lname' => $data['lname'],
            'mail' => $data['mail'],
            'role' => "user",
            'pword' => $passHash,
            'usertoken' => $token,
            'renitoken' => 0,
            'phone' => $data['phone'],
            'otp' => $token,
            'time' => time(),
        ];

        # Build the SQL query
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $columns = implode(', ', array_keys($fields));
        $sql = "INSERT INTO tblusers ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt = $this->conn->prepare($sql);
            $i = 1;
            foreach ($fields as $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($i, ($value), $type);
                $i++;
            }
            $stmt->execute();

            #Add User to subscriber list

            // $subscribeToNewsletter = $this->subscribeToNewsletter();

            #Send verifification mail

            $sendMail = Mailer::sendOTPToken($data['mail'], $data['fname'], $token);

            $output = $this->outputData(true, 'Account created', null, 201);
        } catch (PDOException $e) {

            $output = $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);
        } finally {
            $stmt = null;
            $this->conn = null;

        }

        return $output;
    }

    # updateUserData function updates user biodata

    public function updateUserData(int $id, array $data): int {

        try {
            $updateQuery = 'UPDATE users SET ';
            $params = array();
            foreach ($data as $key => $value) {
                $updateQuery .= $key . ' = ?, ';
                $params[] = $value;
            }
            $updateQuery = rtrim($updateQuery, ', ') . ' WHERE id = ?';
            $params[] = $id;

            $stmt = $this->conn->prepare($updateQuery);
            $stmt->execute($params);

            return $stmt->rowCount();
        } catch (PDOException $e) {
            return $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);
        } finally {
            $stmt = null;
            $this->conn = null;
        }
    }

    public function tryLogin($data) {

        try {
            $sql = 'SELECT * FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':mail', $data['mail'], PDO::PARAM_STR);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (empty($user)) {
                return $this->outputData(false, 'User does not exists', null, 404);
            }

            if ($user['is_verified'] == 0) {
                return $this->outputData(false, "Account not Verified", null, 401);
            }

            if (!password_verify($data['pword'], $user['pword'])) {
                return $this->outputData(false, "Incorrect password for $data[mail]", null, 401);
            }

            // $fetchAccountDetails = $this->fetchAccountDetails($user['renitoken']);

            if ($user) {
                $userData = [
                    'fname' => $user['fname'],
                    'mail' => $user['mail'],
                    'lname' => $user['lname'],
                    'role' => $user['role'],
                    'is_verified' => ($user['is_verified'] == 1) ? true : false,
                    'renitoken' => $user['renitoken'],
                    'usertoken' => $user['usertoken'],
                    // 'accountDetails' => $fetchAccountDetails['data'] ?? [],
                    // 'accountBalance_th' => $fetchAccountDetails['data']['WithdrawableBalance_th'] ?? false,
                    // 'accountBalance' => $fetchAccountDetails['data']['WithdrawableBalance'] ?? false,
                    // 'accountNumber' => $fetchAccountDetails['data']['accountNumber'] ?? false,
                ];
            }
            return $this->outputData(true, 'Login successful', $userData, 200);

        } catch (PDOException $e) {
            return $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);

        } finally {
            $stmt = null;
            $this->conn = null;
        }
    }

#This method checks if  a mail exists
    private function checkIfMailExists(string $mail): bool {

        try {
            $sql = 'SELECT mail FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt = null;
        }
    }

    #Update Password:: This function updates a user Password

    public function updatePassword(array $data) {

        try {
            $sql = 'SELECT pword FROM tblusers WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $data['usertoken'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                $dbPwd = $stmt->fetchColumn();
                $passHash = password_hash($data['npword'], PASSWORD_DEFAULT, [12]);

                if (password_verify($data['fpword'], $dbPwd)) {

                    $updatePasswordInDB = $this->updatePasswordInDB($passHash, $data['usertoken']);

                    if (!$updatePasswordInDB['status']) {
                        $this->outputData(false, $updatePasswordInDB['message'], null, 500);
                        return;
                    }
                    $this->outputData(true, 'Password Updated', null, 200);
                    return;

                } else {

                    return $this->outputData(false, 'Current password specified is not correct', null, 401);

                }
            }
        } catch (PDOException $e) {
            return $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);
        } finally {
            $stmt = null;
            $this->conn = null;

            #   Terminate the database connection
        }
    }

    #verifyUserOTP:: This function does  verifiy a user before unboarding

    public function verifyUserOTP(array $data) {

        try {
            $sql = 'SELECT * FROM tblusers WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':mail', $data['mail'], PDO::PARAM_INT);

            if ($stmt->execute()) {
                $users = $stmt->fetch(PDO::FETCH_ASSOC);

                if (empty($users)) {
                    return $this->outputData(false, 'No user found', null, 404);
                }

                if ($data['otp'] != $users['otp']) {
                    return $this->outputData(false, 'Incorrect OTP', null, 422);
                    exit;
                }

                $activateAccount = $this->activateAccount($data['mail']);
                if (!$activateAccount['success']) {
                    return $this->outputData(false, $activateAccount['message'], null, 422);
                    exit;
                }

                $userData = [
                    'fname' => $users['fname'],
                    'lname' => $users['lname'],
                    'mail' => $users['mail'],
                    'usertoken' => $users['usertoken'],
                    'is_verified' => ($users['is_verified'] == '1') ? true : false,
                    'role' => $users['role'],

                ];
                return $this->outputData(true, "Verification successfull", $userData, 200);
                exit;

            }
        } catch (PDOException $e) {
            return $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);

        } finally {
            $stmt = null;
            $this->conn = null;

            #   Terminate the database connection
        }
    }

    #This methid activates User account  during  verification

    public function activateAccount($mail) {
        $response = ['success' => false, 'message' => ''];

        try {
            $status = 1;
            $sql = 'UPDATE tblusers SET is_verified = :status WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':mail', $mail);

            if ($stmt->execute()) {
                $response['success'] = true;
                // $response['message'] = 'Account activated successfully';
            } else {
                $response['message'] = 'Unable to process request, please try again later';
            }
        } catch (PDOException $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
            // Log the error here or handle it accordingly
        } finally {
            $stmt = null;
            $this->conn = null;
        }

        return $response;
    }

    # updatePasswordInDB::This function Updates users ppassword....

    private function updatePasswordInDB(string $pword, int $usertoken) {
        $response = ['status' => false, 'message' => ''];

        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pword', $pword, PDO::PARAM_STR);
            $stmt->bindParam(':usertoken', $usertoken, PDO::PARAM_INT);
            $stmt->execute();

            $response['status'] = true;
        } catch (PDOException $e) {
            $response['message'] = $e->getMessage();
        } finally {
            $stmt = null;
            $this->conn = null;
        }

        return $response;
    }

#forgetPword: This method handlet password
    public function forgetPword(array $data) {

        $checkIfMailExists = $this->checkIfMailExists($data['mail']);
        if (!$checkIfMailExists) {
            return $this->outputData(false, 'Email does not exists', null, 404);

        }
        $token = Utility::token();

        $passHash = password_hash($token, PASSWORD_DEFAULT);

        if (!$this->resetPasswordInDB($passHash, $data['mail'])) {
            return $this->respondWithInternalError($_SESSION['err']);

        }

        $userData = $this->getUserData($data['mail']);
        if (!$userData['status']) {
            return $this->outputData(false, $userData['message'], null, 500);
        }

        $resetPassword = Mailer::ResetPassword($data['mail'], $userData['data']['fname'], $token);

        return $this->outputData(true, 'Password sent to mail', null, 200);

    }

    private function resetPasswordInDB(string $pword, string $mail): bool {
        try {
            $sql = 'UPDATE tblusers SET pword = :pword WHERE mail = :mail';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':pword', $pword, PDO::PARAM_STR);
            $stmt->bindParam(':mail', $mail, PDO::PARAM_STR);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            $_SESSION['err'] = $e->getMessage();
            return false;
        } finally {
            $stmt = null;
        }
    }

}
