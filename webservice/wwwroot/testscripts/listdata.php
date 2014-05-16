<?php require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');

//

$sqlString = 'SELECT COUNT(*) FROM cyEntry';
$results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

print_r($results);

$sqlString = 'SELECT COUNT(*) FROM cyArticleEntry';
$results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

print_r($results);

$sqlString = 'SELECT * FROM cyEntry LIMIT 5';
$results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

print_r($results);

$sqlString = 'SELECT * FROM cyArticleEntry LIMIT 5';
$results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);

print_r($results);

/*
foreach($results as $result){
    
    echo $result['entryValues'], ' ' , $result['reading_date'], '<br>';
    
    
}
*/



?>