<?php



class Auth
{


  private  static $conn;


  public function __construct(Database $database)
  {
    self::$conn = $database->connect();
  }

  public static  function authenticateAPIKey($api_key): bool
  {
    if (empty($api_key)) {

      http_response_code(400);
      self::outputData(false, 'missing API key', null);
      exit;
    }

    $apptoken = self::validateApiKey($api_key);

    if ($apptoken === false) {

      http_response_code(401);
      self::outputData(false, $_SESSION['err'], null);
      return false;
    }


    return true;
  }



  private  static  function validateApiKey($api_key): bool
  {

    $sql = "SELECT apptoken
    FROM apptoken
    WHERE apptoken = :apptoken";

    $stmt = self::$conn->prepare($sql);

    $stmt->bindParam(':apptoken', $api_key);

    $stmt->execute();

    if ($stmt->rowCount() == 0) {

      $stmt = null;
      $_SESSION['err'] = "No app found.";
      return false;
      // code...
    } else {

      if ($biz = $stmt->fetchAll(PDO::FETCH_ASSOC)) {


        return true;
      } else {
        return false;
      }
    }
  }






  public static  function outputData($success = null, $message = null, $data = null)
  {

    $arr_output = array(
      'success' => $success,
      'message' => $message,
      'data' => $data,
    );
    echo json_encode($arr_output);
  }
}
