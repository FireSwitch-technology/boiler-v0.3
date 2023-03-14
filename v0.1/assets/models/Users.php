<?php       
class Users{
    

  private $conn;
  public function __construct(Database $database){
     
     $this->conn = $database->connect();

  }

/**
 * sanitizeAndValidate Parameters
 *
 * @param array $data
 * @return array
 */
  private  function sanitizeAndValidate($data)
  {
      $errors = [];
      if (empty($data["sample"])) {
          $errors[] = "$data[sample] is required";
      }
      return $errors;
  }


    public function outputData($success = null, $message = null, $data = null)
    {
      
      $arr_output = array(
        'success' => $success,
        'message' => $message,
        'data' => $data,
      );
      echo json_encode($arr_output);
    }
    
}



