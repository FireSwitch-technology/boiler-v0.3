<?php



class Auth extends AbstractClasses
{


  private   $conn;


  public function __construct(Database $database)
  {
    $this->conn = $database->connect();
  }

  
   #authenticate Api Key
  
  public   function authenticateAPIKey(mixed $api_key):bool
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

    // $apptoken = $this->validateApiKey($api_key);

    if ($api_key !== $_ENV['APP_TOKEN']) {

      http_response_code(401); #Unautorized status code
      $this->outputData(false, "No app found", null);
      exit;
      return false;
    }


    return true;
  }


   # validateApiKey:: This fucntion Validate Api Key
  private function validateApiKey( mixed $api_key)
  {
    try {
      $sql = 'SELECT apptoken FROM apptoken WHERE apptoken = :apptoken';
      $stmt = $this->conn->prepare( $sql );
      $stmt->bindParam( ':apptoken', $api_key, PDO::PARAM_STR | PDO::PARAM_INT );
      $stmt->execute();
      if ( $stmt->rowCount() === 0 ) {
          return  false;
      }
      return true;
  } catch ( PDOException $e ) {
      $_SESSION[ 'err' ] = $e->getMessage();
      $this->respondWithInternalError( $_SESSION[ 'err' ]);
  }
  finally {
      $stmt = null;
      $this->conn = null;
  }
  }
  

  
}
