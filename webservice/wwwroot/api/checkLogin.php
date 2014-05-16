<?php

//enforceHTTPS();


session_start();

$CURRENT_USER = NULL;
$PUBLIC = 1;

if
(isset($_SESSION['user']))
{


    $CURRENT_USER  = unserialize($_SESSION['user']);
    
    $response = LoginFactory::validateLogin($user->authkey(), $user->username());
    
    $_SESSION['user'] = serialize($CURRENT_USER );
    $PUBLIC = 0;
    if
    (isset($response->error))
    {   $PUBLIC = 1;
        session_destroy();
        
    }else{
        
        $PUBLIC = 0;
        
    }
    
    

}else
{   $PUBLIC = 1;
    session_destroy();
    
}


?>