<?php



class Auth
{


  private   $conn;


  public function __construct(Database $database)
  {
    $this->conn = $database->connect();
  }

  /**
   * authenticate Api Key
   *
   * @param string $api_key
   * @return boolean
   */
  public   function authenticateAPIKey($api_key)
  {
   
    if (!$api_key) {
      http_response_code(401);
      $this->outputData(false, 'Invalid Authorization header format', null);
      return false;
  }

    if (empty($api_key)) {

      http_response_code(400);
      $this->outputData(false, 'missing API key', null);
      return false;
    }

    $apptoken = $this->validateApiKey($api_key);

    if ($apptoken === false) {

      http_response_code(401);
      $this->outputData(false, $_SESSION['err'], null);
      return false;
    }


    return true;
  }


  /**
   * Validate Api Key
   *
   * @param string $api_key
   * @return boolean
   */
  private function validateApiKey($api_key)
  {
      $isvalidateApiKey = false;
  
      $sql = "SELECT apptoken FROM apptoken WHERE apptoken = :apptoken";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':apptoken', $api_key);
      $stmt->execute();
  
      if ($stmt->rowCount() == 0) {
          $stmt = null;
          $_SESSION['err'] = "No app found.";
          $isvalidateApiKey = false;
      } else {
          if ($biz = $stmt->fetchAll(PDO::FETCH_ASSOC)) {
              $isvalidateApiKey = true;
          } else {
              $isvalidateApiKey = false;
          }
      }
      return $isvalidateApiKey;
  }
  

  public   function outputData($success = null, $message = null, $data = null)
  {

    $arr_output = array(
      'success' => $success,
      'message' => $message,
      'data' => $data,
    );
    echo json_encode($arr_output);
  }
}
