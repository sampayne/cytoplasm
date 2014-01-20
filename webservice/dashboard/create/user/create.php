<?php
include($_SERVER['DOCUMENT_ROOT'] . '/database.php');
session_start();
$username  =$_POST['username'];
$password  =$_POST['password'];
$name      =$_POST['name'];
$group 	   = $_POST['group'];

$database  =new Database;
$sqlString ="INSERT INTO User (name,username,password) VALUES (?,?,?)";
$parameters=array(
    $name,
    $username,
    $password
);


$result = $database->checkExists("User",$username,"username");

if($result){
	
	header('Location:index.php?error=Username already exists');

}else{
	
	

$result = $database->sqlInsert($sqlString, $parameters);
$id = $database->getNewestID("User");

$sqlString = "INSERT INTO GroupUser(userID,groupID) VALUES (?,?)";
$parameters = array($id,$group);
$database->sqlInsert($sqlString, $parameters);

header('Location:/dashboard');

}
?>