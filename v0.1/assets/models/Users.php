<?php
class Users
{


  private $conn;
  public function __construct(Database $database)
  {

    $this->conn = $database->connect();
  }

  /**
   * sanitizeAndValidate Parameters
   *
   * @param array $data
   * @return array
   */
  private  function validateInput($data)
  {
    $errors = [];
    if (empty($data["sample"])) {
      $errors[] = "$data[sample] is required";
    } else {
      $data["sample"] =  $this->sanitizeInput($data["sample"]);
    }
    if (empty($data["data"])) {
      $errors[] = "$data[data] is required";
    } else {
      $data["data"] = $this->sanitizeInput($data["data"]);
    }
    return $errors;
  }

  /**
   * sanitizeInput Parameters
   *
   * @param [type] $input
   * @return string
   */
  private  function sanitizeInput($input)
  {
    // Remove white space from beginning and end of string
    $input = trim($input);
    // Remove slashes
    $input = stripslashes($input);
    // Convert special characters to HTML entities
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    return $input;
  }


  /**
   * respondUnprocessableEntity alert of errors deteced
   *
   * @param array $errors
   * @return void
   */
  private function respondUnprocessableEntity(array $errors): void
  {
    http_response_code(422);
    $this->outputData(false,  'Error encounterd while processing',  $errors);
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
