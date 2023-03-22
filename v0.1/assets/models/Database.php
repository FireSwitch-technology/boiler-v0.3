<?php

ini_set("allow_url_fopen", true);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 0);


if(session_id() == '') {
 session_start();
 } else {}

ob_start();
date_default_timezone_set("Africa/Lagos");

$connect_error = "We sincerely apologise. We are experiencing connection problems";
$mysqli=mysqli_connect( $_ENV['DB_HOST'],$_ENV['DB_USER'],$_ENV['DB_PWORD'],$_ENV['DB_NAME']);
($mysqli)? TRUE : die($connect_error);
global $mysqli;


class Database{
    private $pdo  = null;

    /**
     * connect database
     *
     * @return PDO
     */
    public function connect(): PDO
    {
        if($this->pdo === null){

        $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_NAME']};charset=utf8";
        
        $this->pdo =  new PDO($dsn, $_ENV['DB_USER'],$_ENV['DB_PWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]);
    }
    return $this->pdo;
    }

}





?>
