<?php



class Auth extends SharedModel
{


  private   $conn;


  public function __construct(Database $database)
  {
    $this->conn = $database->connect();
  }


  #authenticate Api Key

  public   function authenticateAPIKey($api_key): bool
  {


    if (empty($api_key)) {
      $_SESSION['err'] = "missing API key";
      return false;
    }


    if ($api_key !== $_ENV['APP_TOKEN']) {

      $_SESSION['err'] = "No app found";
      return false;
    }


    return true;
  }
}
