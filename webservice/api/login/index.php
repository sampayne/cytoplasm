<?php
 header('Access-Control-Allow-Origin: *');  
include_once($_SERVER['DOCUMENT_ROOT'] . "/login_manager.php");
$lm = new LoginManager;
//$id       = $lm->validateLogin("j.doe", "password");
$id = $lm->validateLogin($_POST['username'], $_POST['password']);
if ($id > 0) {
    echo 'LOGIN_SUCCESSFUL';
} else {
    echo 'LOGIN_FAILED';
}
?>