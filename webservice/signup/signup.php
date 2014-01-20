<?php
include($_SERVER['DOCUMENT_ROOT'] . '/database.php');
session_start();
session_destroy();
$username  =$_POST['username'];
$password  =$_POST['password'];
$name      =$_POST['name'];
$database  =new Database;
$sqlString ="INSERT INTO User (name,username,password) VALUES (?,?,?)";
$parameters=array(
    $name,
    $username,
    $password
);


$result = $database->checkExists("User",$username,"username");

if($result){
	
	header('Location: /signup?error=Username already exists');

}else{
	
	

$result = $database->sqlInsert($sqlString, $parameters);
$result=validateLogin($username, $password, $group, $admin);
if($result==-1) {
    echo "ERROR - Login invalid.";
} else {
    session_start();
    $_SESSION['userID']=$result;
    $_SESSION['name']  =$name;
    $_SESSION['type']  =$admin;
    header('Location: /dashboard');
}}
function validateLogin($aUsername="", $aPassword="", $aGroup="", $aAdmin="")
{
    $database=new Database;
    if($aAdmin>0) {
        $query="SELECT id FROM AdminUser WHERE username = '" . $aUsername . "' AND password = '" . $aPassword . "'";
    } else {
        $query="SELECT id FROM User WHERE username = '" . $aUsername . "' AND password = '" . $aPassword . "'";
    }
    $result=$database->mysqlQuery($query);
    if($result[0][0]>-1) {
        return $result[0][0];
    } else {
        return -1;
    }
}
?>