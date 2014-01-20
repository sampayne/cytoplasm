<?php
include_once($_SERVER['DOCUMENT_ROOT']."/database.php");

class DataManager{
	
	private $database;
	
public function __construct(){
	
	$this->database = new Database;
	
	
}

public function getDataScore($aUser){
	
	
	$sqlString = "SELECT Count(id) FROM cyEntry WHERE userID = $aUser";
	$result = $this->database->mysqlQuery($sqlString);
	return $result[0][0];
	
}
	
	
}



?>