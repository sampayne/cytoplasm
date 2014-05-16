<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

require $_SERVER['DOCUMENT_ROOT'] . '/api/include.php';
$requestDaterange_low  = 0;
$requestDaterange_high = 0;
if (isset($_POST['start']) && is_numeric($_POST['start']))
{
    $requestDaterange_low = $_POST['start'];
}
else
{
    $requestDaterange_low = time();
}
if (isset($_POST['end']) && is_numeric($_POST['end']))
{
    $requestDaterange_high = $_POST['end'];
}
else
{
    $requestDaterange_high = time() - 3600;
}
if (isset($_POST['authkey']) && isset($_POST['username']))
{
    $user = LoginFactory::validateLogin($_POST['authkey'], $_POST['username']);
    if (!isset($user->error))
    {
        $article = ArticleFactory::LoadWithID($_POST['article']);

        if ($user->hasPermissionForArticle($article))
        {
            $taxonomy = TaxonomyFactory::LoadWithName($_POST['taxonomy']);
           
            if (!isset($taxonomy->error))
            {
                $data = DataEntryFactory::LoadDataForArticleTaxonomy($article, $taxonomy, $requestDaterange_low, $requestDaterange_high);
       
               
                echo '{ "data":[';
                $i = 0;
                foreach ($data as $entry)
                {
                    if ($i > 0)
                    {
                        echo ',';
                    }
                    echo json_encode($entry->json(), JSON_FORCE_OBJECT);
                    $i++;
                }
                echo ']}';
                die();
            }
        }
        else
        {
            $error = new Error("User does not have permission for that article");
            echo json_encode($error->json(), JSON_FORCE_OBJECT);
            die();
        }
    }
    else
    {
        $error = new Error("Authorisation Failed");
        echo json_encode($error->json(), JSON_FORCE_OBJECT);
        die();
    }
}
else
{
    $error = new Error("Authorisation Failed");
    echo json_encode($error->json(), JSON_FORCE_OBJECT);
    die();
}
?>