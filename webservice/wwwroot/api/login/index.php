<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require $_SERVER['DOCUMENT_ROOT'] . '/api/include.php';
if (isset($_POST['username']) && isset($_POST['password']))
{
    $login = LoginFactory::login($_POST['username'], $_POST['password']);
    echo json_encode($login->json(), JSON_FORCE_OBJECT);
    die();
}
else
{
    $error = new Error('Username or Password Incorrect');
    echo json_encode($error->json(), JSON_FORCE_OBJECT);
    die();
}
$error = new Error('There was a problem');
echo json_encode($error->json(), JSON_FORCE_OBJECT);
die();
?>