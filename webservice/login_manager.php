<?php 

include_once($_SERVER['DOCUMENT_ROOT']."/database.php");

class LoginManager{
	
	
	
	public function validateLogin($aUsername ="", $aPassword=""){
	
	$database = new Database;
	
	$query = "SELECT id FROM User WHERE username = '".$aUsername."' AND password = '".$aPassword."'";
	
	$result = $database->mysqlQuery($query);
	
	if($result[0][0] > 0){
		
		return $result[0][0];
		
	}else{
		
		return -1;
		
	}
	
}

	
	
	
}
