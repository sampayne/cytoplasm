<?php


	include("database.php");
	$database = new Database;
	$sqlString = "SELECT * FROM User WHERE username != 'root'";
	
	$result = $database->mysqlQuery($sqlString);
	
	foreach($result as $user){
		
		
		echo $user['username'],' - ',$user['password'],'<br/>';
		
	}
	


?>