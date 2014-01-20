<?php
 header('Access-Control-Allow-Origin: *');  
$requestDaterange_low  = $_POST['start'];
$requestDaterange_high = $_POST['end'];

if($requestDaterange_high == ""){

$requestDaterange_high = time();
	
	
	
}

if($requestDaterange_low == ""){
	
	$requestDaterange_low = 0;
	
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/database.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/login_manager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/article_manager.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/taxonomy_manager.php';
$loginManager = new LoginManager;
$userID       = $loginManager->validateLogin($_POST['username'], $_POST['password']);
//$userID = 1;
$article = $_POST['article'];
//$article = 39;
if ($userID > 0) {
    $articleManager  = new ArticleManager;
    $taxonomyManager = new TaxonomyManager;
    $taxonomyID      = $taxonomyManager->getTaxonomyID($_POST['taxonomy']);
    //$taxonomyID      = 50;

    if ($taxonomyID > 0) {
        
        $valid = $articleManager->checkUserValid($userID, $article);
        //$valid = $articleManager->checkUserValid($userID, 39);
        
        if ($valid > 0) {
            
            $database  = new Database;
            $sqlString = 'SELECT * FROM cyEntry
								  INNER JOIN ArticleEntry
								  ON cyEntry.id = ArticleEntry.entryID
								  WHERE ArticleEntry.articleID = '.$article . ' AND cyEntry.taxonomyID = ' . $taxonomyID 
								  .' AND cyEntry.reading_date >= '.$requestDaterange_low 
								  .' AND cyEntry.reading_date <= '.$requestDaterange_high 
								  .' ORDER BY cyEntry.reading_date ASC';
								  
            $entries   = $database->mysqlQuery($sqlString);
            
            foreach ($entries as $entry) {
                
               
					echo $entry['entryValues'],'/',$entry['reading_date'],'/';



            }
        } else {
            echo ('ERROR');
        }
    } else {
        
        echo 'ERROR';
        
    }
} else {
    echo 'ERROR';
}
?>