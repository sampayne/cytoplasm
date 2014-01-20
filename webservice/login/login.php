<?php 

include_once($_SERVER['DOCUMENT_ROOT'].'/database.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/login_manager.php');
session_start();
session_destroy();

$loginManager= new LoginManager;
$username = $_POST['username'];
$password = $_POST['password'];

	$result = $loginManager->validateLogin($username,$password);
		
	if($result == -1){
		
		header('Location: /login?error=Login Invalid');
		
	}else{
		
		session_start();
		
		$database = new Database;
		
		$admin = $database->checkExists("GroupAdmin", $result, "userID");
		
		$sqlString = "SELECT (name) FROM User WHERE id =".$result;
		$name = $database->mysqlQuery($sqlString);
		$name = $name[0][0];
		
		$_SESSION['userID'] = $result;
		$_SESSION['name'] = $name;
		$_SESSION['admin'] = $admin;		
		header('Location: /dashboard');		
		
	}	




?>