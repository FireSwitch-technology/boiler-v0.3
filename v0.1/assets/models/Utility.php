<?php

class Utility
{
  #  Function to generate OTP 
  public function generateNumericOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public  static function generateAlphaNumericOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public  static function generateAlphaNumericOTP_case($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public static function generateAlphaNumericOTP_symbol($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "1357902468ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-_@!";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }

  public static function generateAlphaOTP($n)
  {

    #  Take a generator string which consist of 
    #  all numeric digits 
    $generator = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

    #  Iterate for n-times and pick a single character 
    #  from generator and append it to $result 

    #  Login for generating a random character from generator 
    #      ---generate a random number 
    #      ---take modulus of same with length of generator (say i) 
    #      ---append the character at place (i) from generator to result 

    $result = "";

    for ($i = 1; $i <= $n; $i++) {
      $result .= substr($generator, (rand() % (strlen($generator))), 1);
    }

    #  Return result 
    return $result;
  }


  #  validate email
  public  static function validateEmail(string $mail)
  {
    #  code...

    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
      return false;
    }
    return true;
  }




  #formatDate::This method format date to humna readable format

  public static  function formatDate($time)
  {

    return date('D d M, Y: H', $time);
  }

  #v::This method format date to amount readable fomat

  public static function formatCurrency($amonut)
  {

    return number_format($amonut, 2);
  }



  public static function diffForHumans($timestamp)
  {

    $current_time = time();

    $difference_in_seconds = $current_time - $timestamp;

    if ($difference_in_seconds < 60) {
      return "Just now";
    } elseif ($difference_in_seconds < 3600) {
      return floor($difference_in_seconds / 60) . " minutes ago";
    } elseif ($difference_in_seconds < 86400) {
      return floor($difference_in_seconds / 3600) . " hours ago";
    } elseif ($difference_in_seconds < 604800) {
      return floor($difference_in_seconds / 86400) . " days ago";
    } elseif ($difference_in_seconds < 2592000) {
      $weeks = floor($difference_in_seconds / 604800);
      return $weeks . " " . ($weeks === 1 ? "week" : "weeks") . " ago";
    } elseif ($difference_in_seconds < 31104000) {
      return floor($difference_in_seconds / 2592000) . " months ago";
    } else {
      return floor($difference_in_seconds / 31104000) . " years ago";
    }
  }


  public  static function getMemoryUsage()
  {
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1024)
      return $mem_usage . ' bytes';
    elseif ($mem_usage < 1048576)
      return round($mem_usage / 1024, 2) . ' KB';
    else
      return round($mem_usage / 1048576, 2) . ' MB';
  }

  public static function checkSize()
  {
    $memory_usage = self::getMemoryUsage();
    echo 'Memory usage: ' . $memory_usage;
  }


  public static function token()
  {
    return  mt_rand(0000, 5000);
  }


  /**
   * sanitizeInput Parameters
   *
   * @param [ type ] $input
   * @return string
   */
  // public static  function sanitizeInput($input)
  // {
  //   # Remove white space from beginning and end of string
  //   $input = trim($input);
  //   # Remove slashes
  //   $input = stripslashes($input);
  //   # Convert special characters to HTML entities
  //   $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');

  //   return $input;
  // }



  private static  function sanitizeInput($input) {
    // Check if the input is an array
    if (is_array($input)) {
        // Validate and sanitize each element in the array
        foreach ($input as &$element) {
            // Check if the element is not null before applying trim
            if ($element !== null) {
                // Remove white space from beginning and end of each element
                $element = trim($element);
                // Remove slashes
                $element = stripslashes($element);
                // Convert special characters to HTML entities
                $element = htmlspecialchars($element, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            }
        }
        unset($element); // unset to avoid potential side effects

        return $input;
    }

    // For non-array input, continue with the original sanitization process
    // Check if the input is not null before applying trim
    if ($input !== null) {
        // Remove white space from beginning and end of string
        $input = trim($input);
        // Remove slashes
        $input = stripslashes($input);
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    return $input ?? "";
}



#validateFileUpload:: This method validates all images on the platform
public static function validateFileUpload($validKeys, $fieldName) {
  // Initialize the $errors array
  $errors = [];

  $invalidKeys = array_diff(array_keys($_FILES), $validKeys);
  if (!empty($invalidKeys)) {
      foreach ($invalidKeys as $key) {
          $errors[] = "$key is not a valid input field";
      }

      if (!empty($errors)) {
          static::validateRequestParameters($errors);
          return false;
      }
  }

  # Check for required fields
  if (empty($_FILES[$fieldName]['name'])) {
      $errors[] = "$fieldName is required";
  } else {

      # Check file type (extension)
      $allowedExtensions = ['jpeg', 'png'];
      $fileName = $_FILES[$fieldName]['name'];
      $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

      if (!in_array($fileExtension, $allowedExtensions)) {
          $errors[] = 'Invalid file format. Only JPEG and PNG images are allowed.';
      }
  }

  if (!empty($errors)) {
      static::validateRequestParameters($errors);
      return false;
  }

  return true;
}

  public static function validateRequiredParams($data, $validKeys)
  {
    $errors = [];

    #   Check for invalid keys
    $invalidKeys = array_diff(array_keys($data), $validKeys);
    if (!empty($invalidKeys)) {
      foreach ($invalidKeys as $key) {
        $errors[] = "$key is not a valid input field";
      }
    }

    if (!empty($errors)) {
      self::validateRequestParameters($errors);
      return;
    }

    #   Check for empty fields
    foreach ($validKeys as $key) {
      if (empty($data[$key])) {
        $errors[] = ucfirst($key) . ' is required';
      }
    }
    if (!empty($errors)) {
      self::responseToEmptyFields($errors);
      return;
    }

    #   Sanitize input
    foreach ($validKeys as $key) {
      $data[$key] = self::sanitizeInput($data[$key]);
    }

    return $data;
  }

  #  resourceNotFound::Check for id if exists

  private function resourceNotFound(int $id): void
  {

    echo json_encode(['message' => "Resource with id $id not found"]);
  }

  /**
   * validateRequestParameters alert of errors deteced
   *
   * @param array $errors
   * @return void
   */

  public  static  function validateRequestParameters(array $errors): void
  {

    self::outputData(false,  'Kindly review your request parameters to ensure they comply with our requirements.',  $errors);
  }

  public function responseToEmptyFields(array $errors): void
  {

    self::outputData(false,  'All fields are required',  $errors);
  }

  public  static function outputData($success = null, $message = null, $data = null)
  {

    $arr_output = array(
      'success' => $success,
      'message' => $message,
      'data' => $data,
    );
    echo json_encode($arr_output);
  }
}
