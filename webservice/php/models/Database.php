<?php

 class Database
    {
        private $conn;
        
        public function __construct()
        {
            $host       = 'localhost';
            $user       = 'root';
            $pwd        = 'j4ck0f<3S!?';
            $db         = 'test';
            $this->conn = new PDO("mysql:host=$host;dbname=$db", $user, $pwd);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   
        }
        
        public function lastID(){return $this->conn->lastInsertId();}
        
        public function Insert($sqlString, $parameters)
        {
            $stmt = $this->conn->prepare($sqlString);
            if ($stmt) {
                if(!is_null($parameters)){
                for ($i = 1; $i <= count($parameters); $i++) {
                    $stmt->bindValue($i, $parameters[$i - 1]);
                }}
                $stmt->execute();
            } else {
                echo 'ERROR COULD NOT PREPARE STATEMENT';
            }
        }
        public function Query($sqlString, $parameters = NULL)
        {
            $stmt = $this->conn->prepare($sqlString);
            if ($stmt) {
                if(!is_null($parameters)){
                for ($i = 1; $i <= count($parameters); $i++) {
                    $stmt->bindValue($i, $parameters[$i - 1]);
                }}
                $stmt->execute();
               $results = $stmt->fetchAll();
               return $results;

            } else {
                echo 'ERROR COULD NOT PREPARE STATEMENT';
            }

        }

        public function checkExists($table = "", $value = "", $field = "")
        {
            $sqlString = "SELECT COUNT(id) FROM $table WHERE $field = '$value'";
            $stmt      = $this->conn->query($sqlString);
            $result    = $stmt->fetchAll();
            if ($result[0][0] > 0) {
                return 1;
            } else {
                return 0;
            }
        }
    }
?>