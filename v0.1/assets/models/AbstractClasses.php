<?php

abstract class AbstractClasses {

    public  function getMemoryUsage() {
        $mem_usage = memory_get_usage( true );
        if ( $mem_usage < 1024 )
        return $mem_usage.' bytes';
        elseif ( $mem_usage < 1048576 )
        return round( $mem_usage/1024, 2 ).' KB';
        else
        return round( $mem_usage/1048576, 2 ).' MB';
    }

    public  function checkSize() {
        $memory_usage = $this->getMemoryUsage();
        echo 'Memory usage: ' . $memory_usage;
    }

    /**
    * sanitizeInput Parameters
    *
    * @param [ type ] $input
    * @return string
    */
    public  function sanitizeInput( $input )
 {
        # Remove white space from beginning and end of string
        $input = trim( $input );
        # Remove slashes
        $input = stripslashes( $input );
        # Convert special characters to HTML entities
        $input = htmlspecialchars( $input, ENT_QUOTES | ENT_HTML5, 'UTF-8' );

        return $input;
    }

    #  resourceNotFound::Check for id if exists

    private function resourceNotFound( int $id ): void
 {
        http_response_code( 404 );
        echo json_encode( [ 'message' => "Resource with id $id not found" ] );
    }

    /**
    * respondUnprocessableEntity alert of errors deteced
    *
    * @param array $errors
    * @return void
    */

    public function respondUnprocessableEntity(array $errors): void
    {
        http_response_code(400);
        $this->outputData(false,  'Kindly review your request parameters to ensure they comply with our requirements.',  $errors);
    }

    public function connectToThirdPartyAPI( array $payload, string $url)
    {
       
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    
        $response = curl_exec($ch);
        
        if ($response === false) {
            $error = curl_error($ch);
            $this->outputData(false, 'Unable to process request, try again later', null);
        }
    
        curl_close($ch);
    
        return $response;
    }

    
    public function respondWithInternalError($errors ): void
    {
           http_response_code( 500 );
           $this->outputData( false,  "Unable to process request, try again later",  $errors );
       }
   
       public function token()
       {
     
         $defaultPassword = mt_rand(100000, 999999);
         return $defaultPassword;
       }

       public function formatDate($time){

        return date("D d M, Y: H", $time);


    }
    
    public function outputData( $success = null, $message = null, $data = null )
 {

        $arr_output = array(
            'success' => $success,
            'message' => $message,
            'data' => $data,
        );
        echo json_encode( $arr_output );
    }

}