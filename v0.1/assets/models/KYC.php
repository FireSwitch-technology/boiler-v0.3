<?php


class KYC extends Model{

  public function verifyUserKYCInformation($data){


        $hasSubmittedKyc = $this->hasSubmittedKyc($data['usertoken']);
        if ($hasSubmittedKyc['status']) {
         return  $this->outputData(false, 'KYC has been submitted already', null, 409);
        }

         $reniPayLoad = [
           'usertoken' => $data['renitoken'],
           'bvn' => $data['bvn'],
        ];


        $sendReniKYC = $this->updateUserBVN($reniPayLoad);
        if(!$sendReniKYC){
            return $this->outputData(false, $sendReniKYC['message'], null, 500);
        }

        #  Prepare the fields and values for the insert query
        $fields = [
            'renitoken' => $data['renitoken'],
            'usertoken' => $data['usertoken'],
            'fullname' => $data['fname'],
            'occupation' => $data['occupation'],
            'kyc_status' => 0,
            'time' => time()

        ];

          # Build the SQL query
        $placeholders = implode(', ', array_fill(0, count($fields), '?'));
        $columns = implode(', ', array_keys($fields));
        $sql = "INSERT INTO tblkyc ($columns) VALUES ($placeholders)";

        #  Execute the query and handle any errors
        try {
            $stmt =  $this->conn->prepare($sql);
            $i = 1;
            foreach ($fields as $value) {
                $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
                $stmt->bindValue($i,   $value, $type);
                $i++;
            }
            $stmt->execute();

            return  $this->outputData(true, 'KYC verification successful', null, 201);

        } catch (PDOException $e) {

            $this->outputData(false, 'Error: ' . $e->getMessage(), null, 500);
        } finally {
            $stmt = null;
            $this->conn = null;
        }

    }


 
 #updateUserBVN::This method calls reni platform for BVN verification
   public function updateUserBVN($data) {
        $verifyBvnPost = [
            'usertoken' => $data['usertoken'],
            'bvn' => $data['bvn'],
        ];

        $url = $_ENV['RENI_SANDBOX'] . '/updateUserBVN';

        $connectToReniTrust = $this->connectToReniTrust($verifyBvnPost, $url);
        if (isset($connectToReniTrust['success'])) {
            return $connectToReniTrust;
        }

        return $connectToReniTrust;
    }



#hasSubmittedKyc :: this method checks if a user has submitted KYC for verification
   public function hasSubmittedKyc(string $usertoken)
    {
       $response = ['status' => false, 'message' => '', 'status_code' => 200];

        try {
            $sql = 'SELECT COUNT(*) FROM tblkyc WHERE usertoken = :usertoken';
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':usertoken', $usertoken, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                $count = $stmt->fetchColumn();
                
                if ($count > 0) {
                    $response['status'] = true;
                    $response['message'] = "User Kyc submitted";
                } else {
                    $response['message'] = "User Kyc not submitted";
                }
            } else { 
                $response['message'] = "Failed to execute query";
            }
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        } finally {
            $stmt = null;
        }
        return $response;
    }

}