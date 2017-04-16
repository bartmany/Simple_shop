<?php

class db {
    private $host = null;
    private $user = null;
    private $pass = null;
    
    public $conn = null;
    
    public function __construct(
        $host = "localhost",
        $user = "root",
        $pass = "coderslab"
    ){
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        
        try {
            $this->conn = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (Exception $e) {
            echo "Uwaga: " . $e->getMessage() . "<br>";
        }
    }
    
    public function changeDB($name){
        try {
            $this->conn->exec("use $name");
        } catch (Exception $e) {
            echo "Uwaga: " . $e->getMessage() . "<br>";
            return false;
        }
        return true;
    }    
}