<?php
 header('Access-Control-Allow-Origin: *');  
include_once $_SERVER['DOCUMENT_ROOT'] . "/login_manager.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/article_manager.php";
include_once $_SERVER['DOCUMENT_ROOT'] . "/group_manager.php";

$loginManager = new LoginManager;
//$userID       = $loginManager->validateLogin("j.doe", "password");
$userID       = $loginManager->validateLogin($_POST['username'], $_POST['password']);
if ($userID > 0) {
    
    $groupManager   = new GroupManager;
    $articleManager = new ArticleManager;
    
    $groups = $groupManager->getGroups($userID, -1);
    
    foreach ($groups as $group) {
        
        $articles = $articleManager->getArticlesByGroup($group[0]);
        
        foreach ($articles as $article) {
            
            echo $article[0],'/',$article[1],'/',$article[2],'/';
          
        }
        
    }

} else {
    
    echo 'ERROR';
    
    
}

?>