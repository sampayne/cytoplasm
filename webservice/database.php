<?php
class Database {
    private $conn;
    public function __construct() {
        $host      ="gsmtest.cloudapp.net:5986";
        $user      ="root";
        $pwd       ="j4ck0f<3S!?";
        $db        ="test";
        $this->conn=new PDO("mysql:host=$host;dbname=$db", $user, $pwd);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function sqlInsert($sqlString="", $parameters="") {
        $stmt=$this->conn->prepare($sqlString);
        if($stmt) {
            for($i=1; $i<=count($parameters); $i++) {
                $stmt->bindValue($i, $parameters[$i-1]);
            }
            $stmt->execute();
            return $stmt;
        } else {
            echo 'ERROR COULD NOT PREPARE STATEMENT';
        }
    }
    public function mysqlQuery($asqlString) {
        $stmt  =$this->conn->query($asqlString);
        $result=$stmt->fetchAll();
        return $result;
    }
    public function getNewestID($table="") {
        $stmt=$this->conn->query("SELECT MAX(id) AS last_id FROM $table");
        $res =$stmt->fetchAll();
        return $res[0][0];
    }
    public function checkExists($table="", $value="", $field="") {
        $sqlString="SELECT COUNT(id) FROM $table WHERE $field = '$value'";
        $stmt     =$this->conn->query($sqlString);
        $result   =$stmt->fetchAll();
        if($result[0][0]>0) {
            return 1;
        } else {
            return 0;
        }
    }
}
?>