<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
require_once $_SERVER['DOCUMENT_ROOT'] . '/api/include.php';
if (isset($_POST['authkey']) && isset($_POST['username']))
{
    $login = LoginFactory::validateLogin($_POST['authkey'], $_POST['username']);
    if (!isset($login->error))
    {
        
        echo '{ "articles":[';

        $i = 0;
        
        
        foreach ($login->groupArticles(0) as $article)
        {
            if ($i > 0)
            {
                echo ',';
            }
            echo json_encode($article->json(), JSON_FORCE_OBJECT);
            $i++;
        }
        echo ']}';
        die();
      
    }
    else
    {
        echo json_encode($login->json(), JSON_FORCE_OBJECT);
        die();
    }
}
else
{
    $error = new Error('No Auth Key Set');
    echo json_encode($error->json(), JSON_FORCE_OBJECT);
    die();
}
?>