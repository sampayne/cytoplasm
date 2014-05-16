<?php require($_SERVER['DOCUMENT_ROOT'].'/api/include.php');


    $sqlString = 'INSERT INTO cyTaxonomy (name, super) VALUES (?,?)';
    $params = array('bloodpressure', 49);
    DatabaseFactory::getFactory()->getDatabase()->Insert($sqlString, $params);

    
 
 $sqlString = 'SELECT * FROM cyTaxonomy';
 $results = DatabaseFactory::getFactory()->getDatabase()->Query($sqlString);
 
 foreach($results as $result){
     
     
     print_r($result); echo '<br>';
     
 }
 
 
?>